<?php
/**
 * Template for form module
 *
 * $this is an instace of the Form object.
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

echo $this->form_html;
