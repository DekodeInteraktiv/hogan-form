<?php
/**
 * Form module class
 *
 * @package Hogan
 */

namespace Dekode\Hogan;

/**
 * Gravity Forms Form Provider class for Hogan Form
 */
class GravityForms_Provider implements Form_Provider {

	/**
	 * Get provider full name, i.e. "Gravity Forms"
	 *
	 * @return string
	 */
	public function get_name() {
		return 'Gravity Forms';
	}

	/**
	 * Get two character provider identifier, i.e. "gf"
	 *
	 * @return string
	 */
	public function get_identifier() {
		return 'gf';
	}

	/**
	 * Get provider forms as assosiative array
	 *
	 * @return array
	 */
	public function get_forms() {

		if ( true !== $this->enabled() ) {
			return []; // Abort if provider is disabled.
		}

		$forms = \GFAPI::get_forms();
		$array = [];

		if ( is_array( $forms ) && ! empty( $forms ) ) {
			foreach ( $forms as $form ) {
				$array[ $this->get_identifier() . '-' . $form['id'] ] = $form['title'];
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

		if ( true !== $this->enabled() ) {
			return null; // Abort if provider is disabled.
		}

		// Get Gravity Forms form.
		$form = \GFFormsModel::get_form( $id, false );

		if ( empty( $form ) || ! $form->is_active ) {
			return null; // Abort if form doesn't exist or is not published.
		}

		$args_default = [
			'display_title'       => true,
			'display_description' => true,
			'display_inactive'    => false,
			'field_values'        => null,
			'ajax'                => false,
			'tabindex'            => 1,
		];

		$args = apply_filters( 'hogan/module/form/gravityforms/options', [], $id );
		$args = wp_parse_args( $args , $args_default );

		// Return html for the selected form. Inactive or deleted forms will return empty string.
		return gravity_form(
			$id,
			$args['display_title'],
			$args['display_description'],
			$args['display_inactive'],
			$args['field_values'],
			$args['ajax'],
			$args['tabindex'],
			false
		);
	}

	/**
	 * Return if the provider is active or not
	 *
	 * @return boolean
	 */
	public function enabled() {
		return apply_filters( 'hogan/module/form/gravityforms/enabled', true ) &&
		\is_plugin_active( 'gravityforms/gravityforms.php' ) &&
		class_exists( '\GFAPI' );
	}
}

add_action( 'hogan/module/form/include_providers', function( $module ) {
	if ( $module instanceof Form ) {
		$module->register_form_provider( new \Dekode\Hogan\GravityForms_Provider() );
	}
} );
