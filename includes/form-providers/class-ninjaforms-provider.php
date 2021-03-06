<?php
/**
 * Ninja Forms Form Provider class for Hogan Form
 *
 * @package Hogan
 */

declare( strict_types = 1 );
namespace Dekode\Hogan;

if ( ! \interface_exists( '\\Dekode\\Hogan\\Form_Provider' ) ) {
	return;
}

/**
 * Ninja Forms Form Provider class for Hogan Form
 */
class NinjaForms_Provider implements Form_Provider {

	/**
	 * Get provider full name, i.e. "Gravity Forms"
	 *
	 * @return string Provider name
	 */
	public function get_name() : string {
		return 'Ninja Forms';
	}

	/**
	 * Get provider identifier, i.e. "gf"
	 *
	 * @return string Provider indentifier
	 */
	public function get_identifier() : string {
		return 'nf';
	}

	/**
	 * Get provider forms
	 *
	 * @return array Forms as array with identifier and form id as key and form title as value, i.e. [ 'gf-1', 'Form Title' ]
	 */
	public function get_forms() : array {

		$array = [];
		$forms = Ninja_Forms()->form()->get_forms();

		if ( is_array( $forms ) && ! empty( $forms ) ) {
			foreach ( $forms as $form ) {
				$array[ $this->get_identifier() . '-' . $form->get_id() ] = $form->get_setting( 'title' );
			}
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

		if ( ! empty( Ninja_Forms()->form( $id )->get_fields() ) ) {
			return do_shortcode( sprintf( '[ninja_form id=%s]', $id ) );
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
		\is_plugin_active( 'ninja-forms/ninja-forms.php' ) &&
		class_exists( 'Ninja_Forms' ) &&
		apply_filters( 'hogan/module/form/ninja_forms/enabled', true );
	}
}
