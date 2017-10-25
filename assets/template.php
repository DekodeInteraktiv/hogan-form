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

<section class="<?php echo esc_attr( implode( ' ', array_filter( $this->wrapper_classes ) ) ); ?>">
	<article class="columns">
		<h2><?php echo esc_html( $this->heading ); ?></h2>
		<?php
		if ( $this->form ) :
			// Params: id, title, description, display inactive, dynamic params, ajax, tabindex, echo.
			gravity_form( $this->form, false, false, false, '', true );
		endif;
		?>
	</article>
</section>
