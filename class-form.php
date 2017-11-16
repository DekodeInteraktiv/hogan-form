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
		 * Form heading for use in template (optional).
		 *
		 * @var $heading
		 */
		public $heading;

		/**
		 * Select form ID
		 *
		 * @var $form_id
		 */
		public $form_id;

		/**
		 * Form html output for use in template.
		 *
		 * @var $form Html content
		 */
		public $form_html;

		/**
		 * Module constructor.
		 */
		public function __construct() {

			$this->label    = __( 'Form', 'hogan-form' );
			$this->template = __DIR__ . '/assets/template.php';

			parent::__construct();

			add_filter( 'hogan/form/form_value/choices', [ $this, 'append_gravity_form_choices' ] );
			add_action( 'hogan/form/form_value/choices', [ $this, 'append_contact_form_7_choices' ] );
		}

		/**
		 * Append form choices from Gravity Forms
		 *
		 * @param array $choices Array of choices.
		 */
		public function append_gravity_form_choices( $choices ) {

			if ( true !== $this->_is_gravityforms_active() ) {
				return $choices;
			}

			$forms = \GFAPI::get_forms();

			if ( empty( $forms ) ) {
				return $choices;
			}

			foreach ( $forms as $form ) {
				$choices['Gravity Forms'][ 'gf-' . $form['id'] ] = $form['title'];
			}

			return $choices;
		}

		/**
		 * Append form choices from Contact Form 7
		 *
		 * @param array $choices Array of choices.
		 */
		public function append_contact_form_7_choices( $choices ) {

			if ( true !== $this->_is_contact_form_7_active() ) {
				return $choices;
			}

			$forms = get_posts( [
				'post_type'              => 'wpcf7_contact_form',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'posts_per_page'         => 30,
			] );

			if ( empty( $forms ) ) {
				return $choices;
			}

			foreach ( $forms as $form ) {
				$choices['Contact Form 7'][ 'cf-' . $form->ID ] = get_the_title( $form );
			}

			return $choices;
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
				'label'         => __( 'Choose Form', 'hogan-form' ),
				'name'          => 'form_id',
				'instructions'  => __( 'Please select the form you want to show', 'hogan-form' ),
				'choices'       => apply_filters( 'hogan/form/form_value/choices', [] ),
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
			$this->form_id = $content['form_id'];
			$this->form_html = $this->get_form_html( $content['form_id'] );

			parent::load_args_from_layout_content( $content );
		}

		/**
		 * Validate module content before template is loaded.
		 *
		 * @return bool Whether validation of the module is successful / filled with content .
		 */
		public function validate_args() {
			return ! empty( $this->form_id );
		}

		/**
		 * Create html for output
		 *
		 * @return string|bool
		 */
		protected function get_form_html() {

			// Split string into array to find type and id.
			$form_value_array = explode( '-', $this->form_id ); // E.g. $form_value = 'gf-7'.

			if ( count( $form_value_array ) < 2 || ! intval( $form_value_array[1] ) > 0 ) { // Bail early if the second index of the array is not a number.
				return false;
			}

			$form_plugin = $form_value_array[0];
			$form_id     = $form_value_array[1];

			if ( 'gf' === $form_plugin && true === $this->_is_gravityforms_active() && class_exists( '\GFFormsModel' ) ) : // type = Gravity Forms and active plugin.

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
		 * @return bool Whether the plugin is active and available.
		 */
		private function _is_contact_form_7_active() {
			return apply_filters( 'hogan/module/form/contact_form_7/enabled', true ) && \is_plugin_active( 'contact-form-7/wp-contact-form-7.php' );
		}

		/**
		 * Check if a Gravity Forms is an active plugin
		 *
		 * @return bool Whether the plugin is active and available.
		 */
		private function _is_gravityforms_active() {
			return apply_filters( 'hogan/module/form/gravityforms/enabled', true ) && \is_plugin_active( 'gravityforms/gravityforms.php' ) && class_exists( '\GFAPI' );
		}
	}

}
