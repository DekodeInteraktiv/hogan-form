<?php
/**
 * Plugin Name: Hogan Module: Form
 * Plugin URI: https://github.com/dekodeinteraktiv/hogan-form
 * Description: Form Module for Hogan, requires Gravity Forms or Contact Form 7.
 * Version: 1.0.0
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

namespace Dekode\Hogan\Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_textdomain' );
add_action( 'hogan/include_modules', __NAMESPACE__ . '\\register_module' );
add_action( 'hogan/module/form/register_providers', __NAMESPACE__ . '\\register_default_form_providers' );

/**
 * Register module text domain
 */
function load_textdomain() {
	\load_plugin_textdomain( 'hogan-form', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Register module in Hogan
 */
function register_module() {

	// Include form and register module class.
	require_once 'includes/class-form.php';
	\hogan_register_module( new \Dekode\Hogan\Form() );
}

/**
 * Register default form providers
 *
 * @param Form $module Object of Form.
 */
function register_default_form_providers( $module ) {

	if ( ! ( $module instanceof \Dekode\Hogan\Form ) ) {
		return;
	}

	require_once 'includes/form-providers/class-contactform7-provider.php';
	require_once 'includes/form-providers/class-gravityforms-provider.php';
	require_once 'includes/form-providers/class-ninjaforms-provider.php';

	if ( class_exists( '\\Dekode\\Hogan\\ContactForm7_Provider' ) ) {
		$module->register_form_provider( new \Dekode\Hogan\ContactForm7_Provider() );
	}

	if ( class_exists( '\\Dekode\\Hogan\\GravityForms_Provider' ) ) {
		$module->register_form_provider( new \Dekode\Hogan\GravityForms_Provider() );
	}

	if ( class_exists( '\\Dekode\\Hogan\\NinjaForms_Provider' ) ) {
		$module->register_form_provider( new \Dekode\Hogan\NinjaForms_Provider() );
	}
}
