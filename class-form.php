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

			$this->label = __( 'Form', 'hogan-form' );
			$this->template = __DIR__ . '/assets/template.php';

			parent::__construct();
		}

		/**
		 * Field definitions for module.
		 */
		public function get_layout_definition() {

			return [
				'key' => $this->field_key, // hogan_module_form.
				'name' => $this->name,
				'label' => $this->label,
				'display' => 'block',
				'sub_fields' => [
					[
						'type' => 'text',
						'key' => $this->field_key . '_heading', // hogan_module_form_heading.
						'label' => esc_html__( 'Overskrift', 'hogan-form' ),
						'name' => 'heading',
					],
					[
						'type' => 'forms',
						'key' => $this->field_key . '_id', // hogan_module_form_id.
						'label' => esc_html__( 'Velg skjema', 'hogan-form' ),
						'name' => 'form_value',
						'instructions' => __( 'Skjema må opprettes under menypunktet <a href="/wp/wp-admin/admin.php?page=gf_edit_forms">Skjemaer</a> før det kan legges til her.', 'hogan-form' ),
						'return_format' => 'id',
						'allow_null' => 0,
						'multiple' => 0,
					],
				],
			];
		}

		/**
		 * Map fields to object variable.
		 *
		 * @param array $content The content value.
		 */
		public function load_args_from_layout_content( $content ) {

			$this->heading = $content['heading'];
			$this->form = $content['form_value'];

			parent::load_args_from_layout_content( $content );
		}
	}
}
