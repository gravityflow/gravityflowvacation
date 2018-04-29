<?php
/*
Plugin Name: Gravity Flow Vacation Requests Extension
Plugin URI: https://gravityflow.io
Description: Vacation Days Extension for Gravity Flow.
Version: 1.2.1-dev
Author: Gravity Flow
Author URI: https://gravityflow.io
License: GPL-2.0+

------------------------------------------------------------------------
Copyright 2015-2018 Steven Henty

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'GRAVITY_FLOW_VACATION_VERSION', '1.2.1-dev' );

define( 'GRAVITY_FLOW_VACATION_EDD_ITEM_NAME', 'Vacation Requests' );

add_action( 'gravityflow_loaded', array( 'Gravity_Flow_Vacation_Bootstrap', 'load' ), 1 );

class Gravity_Flow_Vacation_Bootstrap {

	public static function load() {
		require_once( 'includes/class-field-vacation.php' );
		require_once( 'includes/class-merge-tag-vacation.php' );
		require_once( 'class-vacation.php' );

		// Registers the class name with GFAddOn.
		GFAddOn::register( 'Gravity_Flow_Vacation' );
	}
}

function gravity_flow_vacation() {
	if ( class_exists( 'Gravity_Flow_Vacation' ) ) {
		return Gravity_Flow_Vacation::get_instance();
	}
}


add_action( 'admin_init', 'gravityflow_vacation_edd_plugin_updater', 0 );

function gravityflow_vacation_edd_plugin_updater() {

	if ( ! function_exists( 'gravity_flow_vacation' ) ) {
		return;
	}

	$gravity_flow_vacation = gravity_flow_vacation();
	if ( $gravity_flow_vacation ) {
		$settings = $gravity_flow_vacation->get_app_settings();

		$license_key = trim( rgar( $settings, 'license_key' ) );

		$edd_updater = new Gravity_Flow_EDD_SL_Plugin_Updater( GRAVITY_FLOW_EDD_STORE_URL, __FILE__, array(
			'version'   => GRAVITY_FLOW_VACATION_VERSION,
			'license'   => $license_key,
			'item_name' => GRAVITY_FLOW_VACATION_EDD_ITEM_NAME,
			'author'    => 'Steven Henty',
		) );
	}

}
