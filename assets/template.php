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
?>

<div class="columns">
	<?php
	if ( ! empty( $this->heading ) ) {
		printf( '<h2>%s</h2>', esc_html( $this->heading ) );
	}
	if ( $this->form ) :
		// Params: id, title, description, display inactive, dynamic params, ajax, tabindex, echo.
		gravity_form( $this->form, false, false, false, '', true );
	endif;
	?>
</div>
