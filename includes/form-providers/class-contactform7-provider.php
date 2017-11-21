<?php
/**
 * Contact Form 7 Form Provider class for Hogan Form
 *
 * @package Hogan
 */

namespace Dekode\Hogan;

if ( ! \interface_exists( '\\Dekode\\Hogan\\Form_Provider' ) ) {
	return;
}

/**
 * Contact Form 7 Form Provider class for Hogan Form
 */
class ContactForm7_Provider implements Form_Provider {

	/**
	 * Get provider full name, i.e. "Gravity Forms"
	 *
	 * @return string Provider name
	 */
	public function get_name() {
		return 'Contact Form 7';
	}

	/**
	 * Get provider identifier, i.e. "gf"
	 *
	 * @return string Provider indentifier
	 */
	public function get_identifier() {
		return 'cf7';
	}

	/**
	 * Get provider forms
	 *
	 * @return array Forms as array with identifier and form id as key and form title as value, i.e. [ 'gf-1', 'Form Title' ]
	 */
	public function get_forms() {

		$array = [];

		$query = new \WP_Query( [
			'post_type'              => 'wpcf7_contact_form',
			'orderby'                => 'title',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'posts_per_page'         => 50,
		] );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$array[ $this->get_identifier() . '-' . get_the_ID() ] = get_the_title();
			}
		}

		wp_reset_postdata();

		return $array;
	}

	/**
	 * Get rendered form HTML
	 *
	 * @param int $id Form ID.
	 * @return string Form HTML
	 */
	public function get_form_html( $id ) {

		$form = get_post( $id );

		if ( $form instanceof \WP_Post && 'wpcf7_contact_form' === $form->post_type ) {
			return do_shortcode( '[contact-form-7 id="' . $id . '"]' );
		}

		return '';
	}

	/**
	 * Finds whether a provider is enabled
	 *
	 * @return boolean Returns TRUE if provider is enabled, FALSE otherwise.
	 */
	public function enabled() {

		// https://codex.wordpress.org/Function_Reference/is_plugin_active .
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		return function_exists( 'is_plugin_active' ) &&
		\is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) &&
		apply_filters( 'hogan/module/form/contact_form_7/enabled', true );
	}
}
