<?php
/**
 * Contact Form 7 Form Provider class for Hogan Form
 *
 * @package Hogan
 */

declare( strict_types = 1 );
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
	public function get_name() : string {
		return 'Contact Form 7';
	}

	/**
	 * Get provider identifier, i.e. "gf"
	 *
	 * @return string Provider indentifier
	 */
	public function get_identifier() : string {
		return 'cf7';
	}

	/**
	 * Get provider forms
	 *
	 * @return array Forms as array with identifier and form id as key and form title as value, i.e. [ 'gf-1', 'Form Title' ]
	 */
	public function get_forms() : array {

		$array = [];

		foreach ( get_posts( [
			'post_type' => 'wpcf7_contact_form',
			'orderby'   => 'title',
		] ) as $form ) {
			$array[ $this->get_identifier() . '-' . $form->ID ] = get_the_title( $form );
		}

		return $array;
	}

	/**
	 * Get rendered form HTML
	 *
	 * @param int $id Form ID.
	 * @return string Form HTML
	 */
	public function get_form_html( int $id ) : string {

		$form = get_post( $id );

		if ( $form instanceof \WP_Post && 'wpcf7_contact_form' === $form->post_type ) {
			return do_shortcode( '[contact-form-7 id="' . $id . '"]' );
		}

		return '';
	}

	/**
	 * Finds whether a provider is enabled
	 *
	 * @return bool Returns TRUE if provider is enabled, FALSE otherwise.
	 */
	public function enabled() : bool {

		// https://codex.wordpress.org/Function_Reference/is_plugin_active .
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		return function_exists( 'is_plugin_active' ) &&
		\is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) &&
		apply_filters( 'hogan/module/form/contact_form_7/enabled', true );
	}
}
