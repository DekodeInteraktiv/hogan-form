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
		 * @var string $heading
		 */
		public $heading;

		/**
		 * Form Provider identifier, i.e. "gf"
		 *
		 * @var string $form_provider_identifier
		 */
		public $form_provider_identifier;

		/**
		 * Select form ID
		 *
		 * @var int $form_id
		 */
		public $form_id;

		/**
		 * Form Providers (Objects that implements interface Form_Provider)
		 *
		 * @var array $_providers
		 */
		private $_providers;

		/**
		 * Module constructor.
		 */
		public function __construct() {

			$this->label    = __( 'Form', 'hogan-form' );
			$this->template = __DIR__ . '/assets/template.php';

			parent::__construct();
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
				'name'          => 'form_info',
				'instructions'  => __( 'Please select the form you want to show', 'hogan-form' ),
				'choices'       => $this->_get_select_field_choices(),
				'ui'            => 1,
				'required'      => 1,
				'ajax'          => 0,
				'return_format' => 'id',
			];

			return $fields;
		}

		/**
		 * Validate module content before template is loaded.
		 *
		 * @return bool Whether validation of the module is successful / filled with content .
		 */
		public function validate_args() {
			return ! empty( $this->form_provider_identifier ) && intval( $this->form_id ) > 0;
		}

		/**
		 * Map fields to object variable.
		 *
		 * @param array $content The content value.
		 */
		public function load_args_from_layout_content( $content ) {

			$this->heading = $content['heading'] ?? null;

			$form_info = explode( '-', $content['form_info'] );

			if ( is_array( $form_info ) && count( $form_info ) == 2 && intval( $form_info[1] ) > 0 ) {
				$this->form_provider_identifier = $form_info[0];
				$this->form_id = intval( $form_info[1] );
			}

			parent::load_args_from_layout_content( $content );
		}

		/**
		 * Register a Form provider
		 *
		 * @param Form_Provider $provider Object that implements interface Form_Provider.
		 */
		public function register_form_provider( $provider ) {
			if ( $provider instanceof Form_Provider ) {
				$this->_providers[] = $provider;
			}
		}

		/**
		 * Get the currently selected Form Provider
		 *
		 * @return Form_Provider $provider
		 */
		private function _get_selected_provider() {

			foreach ( $this->_providers as $provider ) {
				if ( $this->form_provider_identifier === $provider->get_identifier() ) {
					return $provider;
				}
			}

			return null;
		}

		/**
		 * Get aggregated choices from all Form Providers as assositive array for the acf choices value.
		 *
		 * @return array $choices
		 */
		private function _get_select_field_choices() {

			do_action( 'hogan/module/form/include_providers', $this );
			$choices = [];

			if ( is_array( $this->_providers ) && ! empty( $this->_providers ) ) {
				foreach ( $this->_providers as $provider ) {
					$choices[ $provider->get_name() ] = $provider->get_forms();
				}
			}

			return $choices;
		}

		/**
		 * Create html for output
		 *
		 * @return string
		 */
		protected function get_form_html() {

			if ( true !== $this->validate_args() ) {
				return null;
			}

			$provider = $this->_get_selected_provider();

			if ( $provider instanceof Form_Provider ) {
				return $provider->get_form_html( $this->form_id );
			}
		}
	}

}
