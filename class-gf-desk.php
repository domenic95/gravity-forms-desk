<?php
/**
 * Created by PhpStorm.
 * User: dschiera
 * Date: 9/18/15
 * Time: 4:46 PM
 */

if ( class_exists( 'GFForms' ) ) {
	GFForms::include_feed_addon_framework();

	class GFDeskAddOn extends GFFeedAddOn {

		protected $_version = '1.0';
		protected $_min_gravityforms_version = '1.9';
		protected $_slug = 'deskaddon';
		protected $_path = 'deskaddon/deskaddon.php';
		protected $_full_path = __FILE__;
		protected $_url = 'http://www.desk.com';
		protected $_title = 'Gravity Forms Desk Add-On';
		protected $_short_title = 'Desk';

		private static $_instance = null;

		public static function get_instance() {
			if ( self::$_instance == null ) {
				self::$_instance = new self;
			}

			return self::$_instance;
		}

		public function plugin_page() {
			?>
			This page appears in the Forms menu
		<?php
		}

		public function feed_settings_fields() {
			return array(
				array(
					'title'  => 'Desk Form Settings',
					'fields' => array(
						array(
							'label'   => 'My checkbox',
							'type'    => 'checkbox',
							'name'    => 'enabled',
							'tooltip' => 'This is the tooltip',
							'choices' => array(
								array(
									'label' => 'Enabled',
									'name'  => 'enabled'
								)
							)
						),
						array(
							'label'   => 'My checkboxes',
							'type'    => 'checkbox',
							'name'    => 'checkboxgroup',
							'tooltip' => 'This is the tooltip',
							'choices' => array(
								array(
									'label' => 'First Choice',
									'name'  => 'first'
								),
								array(
									'label' => 'Second Choice',
									'name'  => 'second'
								),
								array(
									'label' => 'Third Choice',
									'name'  => 'third'
								)
							)
						),
						array(
							'label'   => 'My Radio Buttons',
							'type'    => 'radio',
							'name'    => 'myradiogroup',
							'tooltip' => 'This is the tooltip',
							'choices' => array(
								array(
									'label' => 'First Choice'
								),
								array(
									'label' => 'Second Choice'
								),
								array(
									'label' => 'Third Choice'
								)
							)
						),
						array(
							'label'      => 'My Horizontal Radio Buttons',
							'type'       => 'radio',
							'horizontal' => true,
							'name'       => 'myradiogrouph',
							'tooltip'    => 'This is the tooltip',
							'choices'    => array(
								array(
									'label' => 'First Choice'
								),
								array(
									'label' => 'Second Choice'
								),
								array(
									'label' => 'Third Choice'
								)
							)
						),
						array(
							'label'   => 'My Dropdown',
							'type'    => 'select',
							'name'    => 'mydropdown',
							'tooltip' => 'This is the tooltip',
							'choices' => array(
								array(
									'label' => 'First Choice',
									'value' => 'first'
								),
								array(
									'label' => 'Second Choice',
									'value' => 'second'
								),
								array(
									'label' => 'Third Choice',
									'value' => 'third'
								)
							)
						),
						array(
							'label'             => 'My Text Box',
							'type'              => 'text',
							'name'              => 'mytext',
							'tooltip'           => 'This is the tooltip',
							'class'             => 'medium',
							'feedback_callback' => array( $this, 'is_valid_setting' )
						),
						array(
							'label'   => 'My Text Area',
							'type'    => 'textarea',
							'name'    => 'mytextarea',
							'tooltip' => 'This is the tooltip',
							'class'   => 'medium merge-tag-support mt-position-right'
						),
						array(
							'label' => 'My Hidden Field',
							'type'  => 'hidden',
							'name'  => 'myhidden'
						),
						array(
							'label' => 'My Custom Field',
							'type'  => 'my_custom_field_type',
							'name'  => 'my_custom_field'
						),
						array(
							'type'           => 'feed_condition',
							'name'           => 'mycondition',
							'label'          => __( 'Opt-In Condition', 'simplefeedaddon' ),
							'checkbox_label' => __( 'Enable Condition', 'simplefeedaddon' ),
							'instructions'   => __( 'Process this example feed if', 'simplefeedaddon' )
						)
					)
				)
			);
		}
	}
}