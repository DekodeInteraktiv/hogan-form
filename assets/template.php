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
if ( $this->form ) :
	if ( 'gravityform' === $this->form_plugin ) {
		// Params: id, title, description, display inactive, dynamic params, ajax, tabindex, echo.
		echo gravity_form( $this->form, false, false, false, '', true, 1, false );
	} elseif ( 'cf7' === $this->form_plugin ) {
		echo do_shortcode( '[contact-form-7 id="' . $this->form . '"]' );
	}
endif;
