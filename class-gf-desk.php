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

        /*
		public function plugin_page() {
			?>
			This page appears in the Forms menu
		<?php
		}
        */

        public function plugin_settings_fields(){
            return array(
                array(
                    'title'  => 'Desk Settings',
                    'fields' => array(
                        array(
                            'label'             => 'Desk.com URL',
                            'type'              => 'text',
                            'name'              => 'url',
                            'tooltip'           => 'The desk.com url of the account.',
                            'class'             => 'medium',
                            'required'           => 1,
                            'feedback_callback' => array( $this, 'is_valid_setting' )
                        ),
                        array(
                            'label'             => 'Username',
                            'type'              => 'text',
                            'name'              => 'username',
                            'tooltip'           => 'Desk.com account username',
                            'class'             => 'medium',
                            'required'           => 1,
                            'feedback_callback' => array( $this, 'is_valid_setting' )
                        ),
                        array(
                            'label'             => 'Password',
                            'type'              => 'text',
                            'input_type'        => 'password',
                            'name'              => 'password',
                            'tooltip'           => 'Desk.com account username',
                            'class'             => 'medium',
                            'required'           => 1,
                            'feedback_callback' => array( $this, 'is_valid_setting' )
                        )
                    )
                )
            );
        }

        public function feed_list_columns(){
            return array('title' => 'Title');
        }

        public function process_feed( $feed, $entry, $form ) {
            $new_feed = array(
                'to'        => $feed['meta']['to'],
                'subject'   => $feed['meta']['subject'],
                'group'     => $feed['meta']['assigned_group'],
                'name'      => $this->get_field_value( $form, $entry, $feed['meta']['mappedFields_name'] ),
                'email'     => $this->get_field_value( $form, $entry, $feed['meta']['mappedFields_email'] ),
                'phone'     => $this->get_field_value( $form, $entry, $feed['meta']['mappedFields_phone'] ),
                'message'   => $this->get_field_value( $form, $entry, $feed['meta']['mappedFields_message'] )
            );

            $resp = $this->create_desk_case($new_feed);
            //$resp = "Desk create case response message.";

            $this->add_note($entry['id'], $resp);
        }

        private function create_desk_case($feed){

            $plugin_settings = $this->get_plugin_settings();

            $message = "Name:\n" . $feed['name'] . "\nPhone:\n" . $feed['phone'] ."\nMessage:\n" . $feed['message'];
            $case = array(
                'type'              => 'email',
                'name'              => $feed['name'],
                'phone'             => $feed['phone'],
                'subject'           => $feed['subject'],
                'status'            => 'new',
                '_links'            => array(
                                        'assigned_group' => array(
                                            'href'  => "/api/v2/groups/{$feed['group']}",
                                            'class' => 'group')),
                'message'           => array(
                                        'direction' => 'in',
                                        'status'    => 'received',
                                        'to'        => $feed['to'],
                                        'from'      => $feed['email'],
                                        'subject'   => $feed['subject'],
                                        'body'      => $message,

                )
            );


            $data = json_encode($case);

            $curl = curl_init("https://{$plugin_settings['url']}/api/v2/cases");
            curl_setopt($curl, CURLOPT_USERPWD, "{$plugin_settings['username']}:{$plugin_settings['password']}");
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json'
            ));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $resp = curl_exec($curl);

            return($resp);

        }

        private function desk_curl($resource, $object = null, $method=1)
        {
            $plugin_settings = $this->get_plugin_settings();
            $data = json_encode($object);

            $curl = curl_init("https://{$plugin_settings['url']}/api/v2/$resource");
            curl_setopt($curl, CURLOPT_USERPWD, "{$plugin_settings['username']}:{$plugin_settings['password']}");
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json'
            ));

            if($method === "GET")
            {
                curl_setopt($curl, CURLOPT_POST, 0);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
            }
            else{
                curl_setopt($curl, CURLOPT_POST, 1);
            }


            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            if($data !== null)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $resp = curl_exec($curl);

            return ($resp);
        }

        private function get_desk_groups_choices(){
            $resp = $this->desk_curl('groups', null, "GET");
            $decode = json_decode($resp);
            $groups = $decode->_embedded->entries;
            $choices = array();
            foreach($groups as $group){
                $choices[] = array(
                    'label' => $group->name,
                    'value' => $group->id
                );
            }
            return $choices;
        }

		public function feed_settings_fields() {
			return array(
				array(
                    'description' => $this->field_map_table_header(),
					'title'  => 'Desk Form Settings',
					'fields' => array(
                        array(
                            'label'             => 'Title',
                            'type'              => 'text',
                            'name'              => 'title',
                            'tooltip'           => 'The title of the feed.',
                            'class'             => 'medium',
                            'required'           => 1,
                            'feedback_callback' => array( $this, 'is_valid_setting' )
                        ),
                        array(
                            'label'             => 'To',
                            'type'              => 'text',
                            'name'              => 'to',
                            'tooltip'           => 'Desk.com email.',
                            'class'             => 'medium',
                            'required'           => 1,
                            'feedback_callback' => array( $this, 'is_valid_setting' )
                        ),
                        array(
                            'label'             => 'Subject',
                            'type'              => 'text',
                            'name'              => 'subject',
                            'tooltip'           => 'The subject of the feed.',
                            'class'             => 'medium',
                            'required'          => 1,
                            'feedback_callback' => array( $this, 'is_valid_setting' )
                        ),
                        array(
                            'label'   => 'Assigned Group',
                            'type'    => 'select',
                            'name'    => 'assigned_group',
                            'tooltip' => 'Group to assign case to.',
                            'choices' => $this->get_desk_groups_choices()
                        ),
                        array(
                            "name" => "mappedFields",
                            "label" => "Map Fields",
                            "type" => "field_map",
                            "field_map" => array(
                                array("name" => "name", "label" => "Name", "required" => 1),
                                array("name" => "phone", "label" => "Phone", "required" => 1),
                                array("name" => "email", "label" => "Email", "required" => 1),
                                array("name" => "message", "label" => "Message", "required" => 1)
                            )
                        )
					)
				)
			);
		}
	}
}