<?php
/**
 * Form module class
 *
 * @package Hogan
 */

namespace Dekode\Hogan;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( '\\Dekode\\Hogan\\Form' ) ) {

	/**
	 * Form module class (Gravity Form).
	 *
	 * @extends Modules base class.
	 */
	class Form extends Module {

		/**
		 * Form plugin used for this module (Gravity Form or Contact Form 7)
		 *
		 * @var $form_plugin
		 */
		public $form_plugin;

		/**
		 * Form id for use in template.
		 *
		 * @var $form
		 */
		public $form;

		/**
		 * Form heading for use in template.
		 *
		 * @var $heading
		 */
		public $heading;

		/**
		 * Module constructor.
		 */
		public function __construct() {

			$this->label       = __( 'Form', 'hogan-form' );
			$this->template    = __DIR__ . '/assets/template.php';
			$this->form_plugin = apply_filters( 'hogan/module/form/plugin_type', 'gravityform' ); //gravityform || cf7

			parent::__construct();


			// Only load gravity module if dependant plugins are active.
			if ( 'gravityform' !== $this->form_plugin || ( function_exists( 'gravity_form' ) && class_exists( 'ACFGravityformsField\Field' ) ) ) {

				// Populate select field with options.
				add_filter( 'acf/load_field/key=' . $this->field_key . '_id', [
					$this,
					'acf_load_field_choices',
				] );
			}
		}

		/**
		 * @return string
		 */
		public function acf_load_field_choices( $field ) {
			// reset choices
			$field['choices'] = [];

			//fetch Contact Form 7 forms
			if ( true === post_type_exists( 'wpcf7_contact_form' ) ) :
				$args  = [
					'posts_per_page'         => 30,
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
					'post_type'              => 'wpcf7_contact_form',
				];
				$query = new \WP_Query( $args );
				// Check that we have query results and add a field group.
				if ( $query->have_posts() ) {
					$field['choices']['Contact Form 7'] = [];
					while ( $query->have_posts() ) {
						$query->the_post();
						// Contents of the queried post results go here.
						$field['choices']['Contact Form 7'][ get_the_ID() ] = get_the_title();
					}
				}

				wp_reset_postdata(); // Restore original post data.
			endif;

			// check for plugin activated
			if ( is_plugin_active( 'gravityforms/gravityforms.php' ) && class_exists( '\GFAPI' ) ) :
				$forms = \GFAPI::get_forms();
				if ( is_array( $forms ) && ! empty( $forms ) ) :
					$field['choices']['Gravityform'] = [];
					foreach ( $forms as $form ) {
						$field['choices']['Gravityform'][ $form['id'] ] = $form['title'];
					}
				endif;
			endif;

			// return the field
			return $field;
		}

		/**
		 * Field definitions for module.
		 */
		public function get_fields() {

			$fields   = [
				[
					'type'  => 'text',
					'key'   => $this->field_key . '_heading', // hogan_module_form_heading.
					'label' => esc_html__( 'Overskrift', 'hogan-form' ),
					'name'  => 'heading',
				],
			];
			$fields[] = [
				'type'          => 'select',
				'key'           => $this->field_key . '_id', // hogan_module_form_id.
				'label'         => esc_html__( 'Velg skjema', 'hogan-form' ),
				'name'          => 'form_value',
				'instructions'  => '',
				'choices'       => [],
				'default_value' => [],
				'ui'            => 1,
				'ajax'          => 0,
				'return_format' => 'id',
			];

			return $fields;
		}

		/**
		 * Map fields to object variable.
		 *
		 * @param array $content The content value.
		 */
		public function load_args_from_layout_content( $content ) {

			$this->heading = $content['heading'];
			$this->form    = $content['form_value'];

			parent::load_args_from_layout_content( $content );
		}
	}
}
