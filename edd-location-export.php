<?php
/*
Plugin Name: Easy Digital Downloads - Location Export
Plugin URI: http://moveplugins.com/edd-location-export
Description: Export payment history by date and location. Useful for tax logs.
Version: 1.0.0.4
Author: Move Plugins
Author URI: http://moveplugins.com
License: GPLv2 or later
Contributors: johnstonphilip, moveplugins, mordauk
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( is_admin() ) {
	
	/**
	 * Include Ajax Functions
	 */
	require( plugin_dir_path( __FILE__ ) . 'includes/misc-functions/ajax-functions.php' );
	
	/**
	 * Include Enqueue Scripts for Admin
	 */
	require( plugin_dir_path( __FILE__ ) . 'includes/misc-functions/enqueue-scripts.php' );
		
	/**
	 * Include form which appears on the Downloads > Reports > Export page
	 */
	require( plugin_dir_path( __FILE__ ) . 'includes/misc-functions/export-form.php' );
	
	/**
	 * Include form which appears on the Downloads > Reports > Export page
	 */
	require( plugin_dir_path( __FILE__ ) . 'includes/misc-functions/export-functions.php' );
	
}
		
	