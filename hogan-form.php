<?php
/**
 * Plugin Name: Hogan Module: Form
 * Plugin URI: https://github.com/dekodeinteraktiv/hogan-form
 * Description: Form Module for Hogan, requires Gravity Forms or Contact Form 7.
 * Version: 1.0.0-dev
 * Author: Dekode
 * Author URI: https://dekode.no
 * License: GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * Text Domain: hogan-form
 * Domain Path: /languages/
 *
 * @package Hogan
 * @author Dekode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'plugins_loaded', 'hogan_form_load_textdomain' );
add_action( 'hogan/include_modules', 'hogan_form_register_module' );

/**
 * Register module text domain
 */
function hogan_form_load_textdomain() {
	\load_plugin_textdomain( 'hogan-form', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Register module in Hogan
 */
function hogan_form_register_module() {

	// Include Form Provider interface and build in providers.
	require_once 'form-providers/interface-form-provider.php';
	require_once 'form-providers/class-gravityforms-provider.php';
	require_once 'form-providers/class-contactform7-provider.php';

	// Include Hogan Module class.
	require_once 'class-form.php';

	\hogan_register_module( new \Dekode\Hogan\Form() );
}
