<?php
/**
 * Plugin Name: Hogan Module: Form
 * Plugin URI: https://github.com/dekodeinteraktiv/hogan-form
 * Description: Form Module for Hogan, requires Gravity Forms.
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

require_once 'class-form.php';

// Only load module if dependant plugins are active.
if ( function_exists( 'gravity_form' ) && class_exists( 'ACFGravityformsField\Field' ) ) {

	add_action( 'plugins_loaded', function() {
		load_plugin_textdomain( 'hogan-form', false, '/languages' );
	} );

	add_action( 'hogan/include_modules', function() {
		hogan_register_module( new \Dekode\Hogan\Form() );
	} );
}
