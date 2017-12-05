<?php
/**
 * Form Module tempalte
 *
 * $this is an instace of the Form object.
 *
 * Available properties:
 * $this->heading (string) Module heading.
 * $this->select_provider (Form_Provider) Form provider object.
 * $this->selected_form_id (int) Form ID.
 * $this->get_form_html() (string) Form HTML from provider.
 *
 * @package Hogan
 */

namespace Dekode\Hogan;

if ( ! defined( 'ABSPATH' ) || ! ( $this instanceof Form ) ) {
	return; // Exit if accessed directly.
}

if ( ! empty( $this->heading ) ) {
	printf( '<h2>%s</h2>', esc_html( $this->heading ) );
}

// @codingStandardsIgnoreStart
// In form builder developers we trust. No need to validate/escape any data here.
echo $this->get_form_html();
// @codingStandardsIgnoreEnd
