<?php
/**
 * Enqueue Admin Scripts
 *
 * @since 1.0
 * @return void
 */
function edd_location_export_admin_enqueue(){
	wp_enqueue_script( 'edd_location_admin_ajax', plugins_url('js/edd-location-export-admin-ajax.js', dirname(__FILE__) ), array( 'jquery' ) );
	
	wp_localize_script( 'edd_location_admin_ajax', 'edd_global_vars', array(
		'ajaxurl' => edd_get_ajax_url(),
	));
}
add_action( 'admin_enqueue_scripts', 'edd_location_export_admin_enqueue' );