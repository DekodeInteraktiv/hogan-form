<?php
/**
 * Form module class.
 *
 * @package Hogan
 */

declare( strict_types = 1 );
namespace Dekode\Hogan;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( '\\Dekode\\Hogan\\Form' ) && class_exists( '\\Dekode\\Hogan\\Module' ) ) {

	/**
	 * Form module class.
	 *
	 * @extends Modules base class.
	 */
	class Form extends Module {

		/**
		 * Form Provider
		 *
		 * @var Form_Provider $select_provider
		 */
		public $select_provider;

		/**
		 * Select form ID
		 *
		 * @var int $selected_form_id
		 */
		public $selected_form_id;

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
			$this->template = HOGAN_FORM_PATH . 'assets/template.php';

			parent::__construct();
		}

		/**
		 * Field definitions for module.
		 *
		 * @return array $fields Fields for this module
		 */
		public function get_fields() : array {

			$fields = [];

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
		 * @return bool Whether validation of the module is successful / filled with content.
		 */
		public function validate_args() : bool {
			return intval( $this->selected_form_id ) > 0 &&
				$this->select_provider instanceof Form_Provider &&
				true === $this->select_provider->enabled();
		}

		/**
		 * Map raw fields from acf to object variable.
		 *
		 * @param array $raw_content Content values.
		 * @param int   $counter Module location in page layout.
		 * @return void
		 */
		public function load_args_from_layout_content( array $raw_content, int $counter = 0 ) {

			$form_info = explode( '-', $raw_content['form_info'] );

			if ( is_array( $form_info ) && count( $form_info ) === 2 && intval( $form_info[1] ) > 0 ) {
				// Set provider and form id.
				$this->select_provider = $this->_get_provider( $form_info[0] );
				$this->selected_form_id = intval( $form_info[1] );
			}

			parent::load_args_from_layout_content( $raw_content, $counter );
		}

		/**
		 * Register a Form provider
		 *
		 * @param Form_Provider $provider Object that implements interface Form_Provider.
		 * @return void
		 */
		public function register_form_provider( Form_Provider $provider ) {
			$this->_providers[] = $provider;
		}

		/**
		 * Get the currently selected Form Provider
		 *
		 * @param string $identifier Provider identifier.
		 * @return Form_Provider|null $provider Provider instance.
		 */
		private function _get_provider( string $identifier ) {

			if ( is_array( $this->_providers ) && ! empty( $this->_providers ) ) {
				foreach ( $this->_providers as $provider ) {
					if ( $identifier === $provider->get_identifier() ) {
						return $provider;
					}
				}
			}

			return null;
		}

		/**
		 * Get aggregated choices from all Form Providers as assositive array for the acf choices value.
		 *
		 * @return array $choices
		 */
		private function _get_select_field_choices() : array {

			// Include Form Provider interface before including form providers.
			require_once 'interface-form-provider.php';

			do_action( 'hogan/module/form/register_providers', $this );
			$choices = [];

			if ( is_array( $this->_providers ) && ! empty( $this->_providers ) ) {
				foreach ( $this->_providers as $provider ) {
					if ( true === $provider->enabled() ) {
						$provider_forms = $provider->get_forms();

						if ( is_array( $provider_forms ) && ! empty( $provider_forms ) ) {
							$choices[ $provider->get_name() ] = $provider_forms;
						}
					}
				}
			}

			return $choices;
		}

		/**
		 * Get Form HTML
		 *
		 * @return string Form HTML
		 */
		protected function get_form_html() : string {

			if ( true === $this->validate_args() ) {
				return $this->select_provider->get_form_html( $this->selected_form_id );
			}

		}
	}

}
