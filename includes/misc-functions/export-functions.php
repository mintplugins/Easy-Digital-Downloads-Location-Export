<?php
/**
 * Exports Functions
 *
 * These are functions are used for exporting data from Easy Digital Downloads - Locaton Export
 *
 * @package     EDD
 * @subpackage  Admin/Export
 * @copyright   Copyright (c) 2013, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Exports all the payments stored in Payment History to a CSV file using the
 * EDD_Export class.
 *
 * @since 1.4.4
 * @return void
 */
function edd_export_payment_history_by_location() {
	
	require_once EDD_PLUGIN_DIR . 'includes/admin/reporting/class-export.php';
	
	require_once plugin_dir_path( __FILE__ ) . 'export-class.php';
		
	$payments_export = new EDD_Payments_By_Location_Export();

	$payments_export->export();
}
add_action( 'edd_payments_by_location_export', 'edd_export_payment_history_by_location' );