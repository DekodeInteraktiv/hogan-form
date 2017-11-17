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
	 * @return string
	 */
	public function get_name();

	/**
	 * Get two character provider identifier, i.e. "gf"
	 *
	 * @return string
	 */
	public function get_identifier();

	/**
	 * Get provider forms as assosiative array
	 *
	 * @return array
	 */
	public function get_forms();

	/**
	 * Get rendered form HTML
	 *
	 * @param int $id Form Id.
	 * @return string
	 */
	public function get_form_html( $id );

	/**
	 * Return if the provider is active or not
	 *
	 * @return boolean
	 */
	public function enabled();
}
