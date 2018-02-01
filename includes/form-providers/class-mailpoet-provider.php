<?php
/**
 * MailPoet Form Provider class for Hogan Form
 *
 * @package Hogan
 */

declare( strict_types = 1 );

namespace Dekode\Hogan;

if ( ! \interface_exists( '\\Dekode\\Hogan\\Form_Provider' ) ) {
	return;
}

/**
 * MailPoet Form Provider class for Hogan Form
 */
class MailPoet_Provider implements \Dekode\Hogan\Form_Provider {

	/**
	 * Get provider full name, i.e. "Gravity Forms"
	 *
	 * @return string Provider name
	 */
	public function get_name() : string {
		return 'MailPoet Forms';
	}

	/**
	 * Get provider identifier, i.e. "gf"
	 *
	 * @return string Provider indentifier
	 */
	public function get_identifier() : string {
		return 'mp';
	}

	/**
	 * Get provider forms
	 *
	 * @return array Forms as array with identifier and form id as key and form title as value, i.e. [ 'gf-1', 'Form Title' ]
	 */
	public function get_forms() : array {
		$array = [];

		$forms = \MailPoet\Models\Form::getPublished()->orderByAsc( 'name' )->findArray();
		foreach ( $forms as $form ) {
			$array[ $this->get_identifier() . '-' . $form['id'] ] = $form['name'];
		}

		return $array;
	}

	/**
	 * Get rendered form HTML
	 *
	 * @param int $id Form ID.
	 *
	 * @return string Form HTML
	 */
	public function get_form_html( int $id ) : string {
		$form_widget = new \MailPoet\Form\Widget();

		return $form_widget->widget(
			[
				'form'      => $id,
				'form_type' => 'php',
			]
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
			   \is_plugin_active( 'mailpoet/mailpoet.php' ) &&
			   class_exists( '\\MailPoet\\Models\\Form' ) &&
			   class_exists( '\\MailPoet\\Form\\Widget' ) &&
			   apply_filters( 'hogan/module/form/mailpoet/enabled', true );
	}
}
