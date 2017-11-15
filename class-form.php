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
	 * Form module class (Gravity Form or Contact Form 7).
	 *
	 * @extends Modules base class.
	 */
	class Form extends Module {

		/**
		 * Form html output for use in template.
		 *
		 * @var $form Html content
		 */
		public $form_html;

		/**
		 * Form heading for use in template (optional).
		 *
		 * @var $heading
		 */
		public $heading;

		/**
		 * Module constructor.
		 */
		public function __construct() {

			$this->label    = __( 'Form', 'hogan-form' );
			$this->template = __DIR__ . '/assets/template.php';

			parent::__construct();

			// Populate select field with options.
			add_filter( 'acf/load_field/key=' . $this->field_key . '_id', [
				$this,
				'acf_load_field_choices',
			] );

		}

		/**
		 * Populate select field with forms items
		 *
		 * @param    $field - the field array holding all the field options
		 *
		 * @return    $field - the field array holding all the field options
		 */
		public function acf_load_field_choices( $field ) {
			// reset choices
			$field['choices'] = [];

			// Check for Contact Form 7 and fetch forms
			if ( true === $this->_is_contact_form_7_active() ) :
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
						$field['choices']['Contact Form 7'][ 'cf-' . get_the_ID() ] = get_the_title();
					}
				}

				wp_reset_postdata(); // Restore original post data.
			endif;

			// Check for Gravityform and fetch forms
			if ( true === $this->_is_gravityforms_active() && class_exists( '\GFAPI' ) ) :
				$forms = \GFAPI::get_forms();
				if ( is_array( $forms ) && ! empty( $forms ) ) :
					$field['choices']['Gravityform'] = [];
					foreach ( $forms as $form ) {
						$field['choices']['Gravityform'][ 'gf-' . $form['id'] ] = $form['title'];
					}
				endif;
			endif;

			// return the field
			return $field;
		}

		/**
		 * Field definitions for module.
		 *
		 * @return array $fields Fields for this module
		 */
		public function get_fields() {

			$fields = [];

			// Heading field can be disabled using filter hogan/module/form/heading/enabled (true/false).
			hogan_append_heading_field( $fields, $this );

			$fields[] = [
				'type'          => 'select',
				'key'           => $this->field_key . '_id', // hogan_module_form_id.
				'label'         => esc_html__( 'Choose Form', 'hogan-form' ),
				'name'          => 'form_value',
				'instructions'  => '', //todo?
				'choices'       => [],
				'default_value' => [],
				'ui'            => 1,
				'required'      => 1,
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
			$this->heading = $content['heading'] ?? null;
			$this->form_html = $this->get_form_html( $content['form_value'] );

			parent::load_args_from_layout_content( $content );
		}

		/**
		 * Validate module content before template is loaded.
		 *
		 * @return bool Whether validation of the module is successful / filled with content .
		 */
		public function validate_args() {
			return ! empty( $this->form_html ); //note: could be improved, eg. Missing contact form 7 will return '[contact-form-7 404 "Not Found"]'
		}

		/**
		 * Create html for output
		 *
		 * @param $form_value Value from the selected field
		 *
		 * @return string|bool
		 */
		protected function get_form_html( $form_value ) {
			//break string into array to find type and id
			$form_value_array = explode( '-', $form_value ); //E.g. $form_value = 'gf-7'

			if ( count( $form_value_array ) < 2 || ! intval( $form_value_array[1] ) > 0 ) { // Bail early if the second index of the array is not a number
				return false;
			}

			$form_plugin = $form_value_array[0];
			$form_id     = $form_value_array[1];

			if ( 'gf' === $form_plugin && true === $this->_is_gravityforms_active() && class_exists( '\GFFormsModel' ) ) : //type = Gravityforms and active plugin
				// Check if form is exists and is active
				$form_info = \GFFormsModel::get_form( $form_id, false );

				if ( empty( $form_info ) ) { // No published forms - stop
					return false;
				}

				$gs_defaults = [
					'display_title'       => true,
					'display_description' => true,
					'display_inactive'    => false,
					'field_values'        => null,
					'ajax'                => false,
					'tabindex'            => 1,
				];

				// Merge $args from filter with $defaults
				$args = wp_parse_args( apply_filters( 'hogan/module/form/gravityforms/options', [], $form_id ), $gs_defaults );

				// Return html for the selected form. Inactive or deleted forms will return empty string
				return gravity_form(
					$form_id,
					$args['display_title'],
					$args['display_description'],
					$args['display_inactive'],
					$args['field_values'],
					$args['ajax'],
					$args['tabindex'],
					false
				);
			elseif ( 'cf' === $form_plugin && true === $this->_is_contact_form_7_active() ) : //type = Contact Form 7 and active plugin
				// Check if form exists and is active
				$query = new \WP_Query( array(
					'p'                      => $form_id,
					'post_type'              => 'wpcf7_contact_form',
					'fields'                 => 'ids',
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
				) );

				return ( $query->have_posts() ) ? do_shortcode( '[contact-form-7 id="' . $form_id . '"]' ) : false;
			else :
				return false;
			endif;
		}

		/**
		 * Check if a Contact Form 7 is an active plugin
		 *
		 * @return bool Whether the plugin is active is registered.
		 */
		private function _is_contact_form_7_active() {
			return apply_filters( 'hogan/module/form/gravityforms/enabled', true) && post_type_exists( 'wpcf7_contact_form' );
		}

		/**
		 * Check if a Gravity Forms is an active plugin
		 *
		 * @return bool Whether the plugin is active is registered.
		 */
		private function _is_gravityforms_active() {
			return apply_filters( 'hogan/module/form/gravityforms/enabled', true) && is_plugin_active( 'gravityforms/gravityforms.php' );
		}
	}
} // End if().
