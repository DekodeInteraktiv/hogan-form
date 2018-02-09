<?php
/**
 * Gravity Forms Form Provider class for Hogan Form
 *
 * @package Hogan
 */

declare( strict_types = 1 );
namespace Dekode\Hogan;

if ( ! \interface_exists( '\\Dekode\\Hogan\\Form_Provider' ) ) {
	return;
}

/**
 * Gravity Forms Form Provider class for Hogan Form
 */
class GravityForms_Provider implements Form_Provider {

	/**
	 * Get provider full name, i.e. "Gravity Forms"
	 *
	 * @return string Provider name
	 */
	public function get_name() : string {
		return 'Gravity Forms';
	}

	/**
	 * Get provider identifier, i.e. "gf"
	 *
	 * @return string Provider indentifier
	 */
	public function get_identifier() : string {
		return 'gf';
	}

	/**
	 * Get provider forms
	 *
	 * @return array Forms as array with identifier and form id as key and form title as value, i.e. [ 'gf-1', 'Form Title' ]
	 */
	public function get_forms() : array {

		$array = [];
		$forms = \GFAPI::get_forms();

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
	 * @param int $id Form ID.
	 * @return string Form HTML
	 */
	public function get_form_html( int $id ) : string {

		// Get Gravity Forms form.
		$form = \GFFormsModel::get_form( $id, false );

		if ( empty( $form ) || ! $form->is_active ) {
			return ''; // Abort if form doesn't exist or is not published.
		}

		$args = apply_filters( 'hogan/module/form/gravityforms/options', [], $id );
		$args = wp_parse_args( $args, [
			'display_title'       => false,
			'display_description' => false,
			'display_inactive'    => false,
			'field_values'        => null,
			'ajax'                => false,
			'tabindex'            => 1,
		] );

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
	 * Finds whether a provider is enabled
	 *
	 * @return bool Returns TRUE if provider is enabled, FALSE otherwise.
	 */
	public function enabled() : bool {

		// https://codex.wordpress.org/Function_Reference/is_plugin_active .
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		return function_exists( 'is_plugin_active' ) &&
		\is_plugin_active( 'gravityforms/gravityforms.php' ) &&
		class_exists( '\GFAPI' ) &&
		apply_filters( 'hogan/module/form/gravity_forms/enabled', true );
	}
}
