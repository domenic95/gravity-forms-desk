<?php

/*
Plugin Name: Gravity Forms + Desk
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: dschiera
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

define( 'GF_DESK_VERSION', '1.0.0' );

add_action( 'gform_loaded', array( 'GF_Desk_Bootstrap', 'load' ), 5 );

class GF_Desk_Bootstrap {

	public static function load(){

		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			return;
		}

		require_once( 'class-gf-desk.php' );

		GFAddOn::register( 'GFDeskAddOn' );
	}

}

function gf_desk() {
	return GFDeskAddOn::get_instance();
}