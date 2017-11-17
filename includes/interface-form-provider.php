<?php
/**
 * Form Provider interface
 *
 * @package Hogan
 */

namespace Dekode\Hogan;

/**
 * Form Provider interface
 */
interface Form_Provider {

	/**
	 * Get provider full name, i.e. "Gravity Forms"
	 *
	 * @return string Provider name
	 */
	public function get_name();

	/**
	 * Get provider identifier, i.e. "gf"
	 *
	 * @return string Provider indentifier
	 */
	public function get_identifier();

	/**
	 * Get provider forms
	 *
	 * @return array Forms as array with identifier and form id as key and form title as value, i.e. [ 'gf-1', 'Form Title' ]
	 */
	public function get_forms();

	/**
	 * Get rendered form HTML
	 *
	 * @param int $id Form ID.
	 * @return string Form HTML
	 */
	public function get_form_html( $id );

	/**
	 * Finds whether a provider is enabled
	 *
	 * @return boolean Returns TRUE if provider is enabled, FALSE otherwise.
	 */
	public function enabled();
}
