<?php
/**
 * Contact Form 7 Form Provider class for Hogan Form
 *
 * @package Hogan
 */

namespace Dekode\Hogan;

/**
 * Contact Form 7 Form Provider class for Hogan Form
 */
class ContactForm7_Provider implements Form_Provider {

	/**
	 * Get provider full name, i.e. "Gravity Forms"
	 *
	 * @return string
	 */
	public function get_name() {
		return 'Contact Form 7';
	}

	/**
	 * Get two character provider identifier, i.e. "gf"
	 *
	 * @return string
	 */
	public function get_identifier() {
		return 'cf';
	}

	/**
	 * Get provider forms as assosiative array
	 *
	 * @return array
	 */
	public function get_forms() {

		$array = [];
		$forms = get_posts( [
			'post_type'              => 'wpcf7_contact_form',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'posts_per_page'         => 30,
		] );

		if ( is_array( $forms ) && ! empty( $forms ) ) {
			foreach ( $forms as $form ) {
				$array[ $this->get_identifier() . '-' . $form->ID ] = get_the_title( $form );
			}
		}

		return $array;
	}

	/**
	 * Get rendered form HTML
	 *
	 * @param int $id Form Id.
	 * @return string
	 */
	public function get_form_html( $id ) {

		$form = get_post( $id );

		if ( $form instanceof \WP_Post && 'wpcf7_contact_form' === $form->post_type ) {
			return do_shortcode( '[contact-form-7 id="' . $id . '"]' );
		}
	}

	/**
	 * Return if the provider is active or not
	 *
	 * @return boolean
	 */
	public function enabled() {
		return apply_filters( 'hogan/module/form/contact_form_7/enabled', true ) &&
		\is_plugin_active( 'contact-form-7/wp-contact-form-7.php' );
	}
}
