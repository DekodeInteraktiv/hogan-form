<?php
/**
 * Plugin Name: Hogan Module: Form
 * Plugin URI: https://github.com/dekodeinteraktiv/hogan-form
 * GitHub Plugin URI: https://github.com/dekodeinteraktiv/hogan-form
 * Description: Form Module for Hogan, requires Gravity Forms, Contact Form 7, Ninja Forms or MailPoet Forms.
 * Version: 1.1.3
 * Author: Dekode
 * Author URI: https://dekode.no
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * Text Domain: hogan-form
 * Domain Path: /languages/
 *
 * @package Hogan
 * @author Dekode
 */

declare( strict_types = 1 );
namespace Dekode\Hogan\Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HOGAN_FORM_PATH', plugin_dir_path( __FILE__ ) );

add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_textdomain' );
add_action( 'hogan/include_modules', __NAMESPACE__ . '\\register_module', 10, 1 );
add_action( 'hogan/module/form/register_providers', __NAMESPACE__ . '\\register_default_form_providers' );

/**
 * Register module text domain
 *
 * @return void
 */
function load_textdomain() {
	\load_plugin_textdomain( 'hogan-form', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Register module in Hogan
 *
 * @param \Dekode\Hogan\Core $core Hogan Core instance.
 * @return void
 */
function register_module( \Dekode\Hogan\Core $core ) {
	require_once 'includes/class-form.php';
	$core->register_module( new \Dekode\Hogan\Form() );
}

/**
 * Register default form providers
 *
 * @param \Dekode\Hogan\Form $module Form instance.
 */
function register_default_form_providers( \Dekode\Hogan\Form $module ) {

	require_once 'includes/form-providers/class-contactform7-provider.php';
	require_once 'includes/form-providers/class-gravityforms-provider.php';
	require_once 'includes/form-providers/class-ninjaforms-provider.php';
	require_once 'includes/form-providers/class-mailpoet-provider.php';

	if ( class_exists( '\\Dekode\\Hogan\\ContactForm7_Provider' ) ) {
		$module->register_form_provider( new \Dekode\Hogan\ContactForm7_Provider() );
	}

	if ( class_exists( '\\Dekode\\Hogan\\GravityForms_Provider' ) ) {
		$module->register_form_provider( new \Dekode\Hogan\GravityForms_Provider() );
	}

	if ( class_exists( '\\Dekode\\Hogan\\NinjaForms_Provider' ) ) {
		$module->register_form_provider( new \Dekode\Hogan\NinjaForms_Provider() );
	}

	if ( class_exists( '\\Dekode\\Hogan\\MailPoet_Provider' ) ) {
		$module->register_form_provider( new \Dekode\Hogan\MailPoet_Provider() );
	}
}
