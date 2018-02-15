<?php
/**
 * Form Module template
 *
 * $this is an instace of the Form object.
 *
 * Available properties:
 * $this->select_provider (Form_Provider) Form provider object.
 * $this->selected_form_id (int) Form ID.
 * $this->get_form_html() (string) Form HTML from provider.
 *
 * @package Hogan
 */

declare( strict_types = 1 );
namespace Dekode\Hogan;

if ( ! defined( 'ABSPATH' ) || ! ( $this instanceof Form ) ) {
	return; // Exit if accessed directly.
}

// In form builder developers we trust. No need to validate/escape any data here.
echo $this->get_form_html(); // WPCS: XSS OK.
