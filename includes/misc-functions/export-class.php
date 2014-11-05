<?php
/**
 * Payments By Location Export Class
 *
 * This class handles payment export
 *
 * @package     EDD Export By Location
 * @subpackage  Admin/Reports
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.4.4
 */

/**
 * EDD_Payments_By_Location_Export Class
 *
 * @since 1.4.4
 */
class EDD_Payments_By_Location_Export extends EDD_Export {
	/**
	 * Our export type. Used for export-type specific filters/actions
	 * @var string
	 * @since 1.4.4
	 */
	public $export_type = 'payments_by_location';

	/**
	 * Set the export headers
	 *
	 * @access public
	 * @since 1.6
	 * @return void
	 */
	public function headers() {
		ignore_user_abort( true );

		if ( ! edd_is_func_disabled( 'set_time_limit' ) && ! ini_get( 'safe_mode' ) )
			set_time_limit( 0 );

		$month = isset( $_POST['month'] ) ? absint( $_POST['month'] ) : date( 'n' );
		$year  = isset( $_POST['year']  ) ? absint( $_POST['year']  ) : date( 'Y' );

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=edd-export-' . $this->export_type . '-' . $month . '-' . $year . '.csv' );
		header( "Expires: 0" );
	}

	/**
	 * Set the CSV columns
	 *
	 * @access public
	 * @since 1.4.4
	 * @return array $cols All the columns
	 */
	public function csv_cols() {
		global $edd_options;

		$cols = array(
			'id'       => __( 'ID',   'edd-location-export' ),
			'email'    => __( 'Email', 'edd-location-export' ),
			'first'    => __( 'First Name', 'edd-location-export' ),
			'last'     => __( 'Last Name', 'edd-location-export' ),
			'products' => __( 'Products', 'edd-location-export' ),
			'skus'     => __( 'SKUs', 'edd-location-export' ),
			'amount'   => __( 'Amount', 'edd-location-export' ) . ' (' . html_entity_decode( edd_currency_filter( '' ) ) . ')',
			'tax'      => __( 'Tax', 'edd-location-export' ) . ' (' . html_entity_decode( edd_currency_filter( '' ) ) . ')',
			'discount' => __( 'Discount Code', 'edd-location-export' ),
			'gateway'  => __( 'Payment Method', 'edd-location-export' ),
			'key'      => __( 'Purchase Key', 'edd-location-export' ),
			'date'     => __( 'Date', 'edd-location-export' ),
			'user'     => __( 'User', 'edd-location-export' ),
			'status'   => __( 'Status', 'edd-location-export' ),
			'country'   => __( 'Country', 'edd-location-export' ),
			'state'   => __( 'State', 'edd-location-export' ),
			'city'   => __( 'City', 'edd-location-export' )
		);

		if( ! edd_use_skus() )
			unset( $cols['skus'] );

		return $cols;
	}

	/**
	 * Get the Export Data
	 *
	 * @access public
	 * @since 1.4.4
	 * @global object $wpdb Used to query the database using the WordPress
	 *   Database API
	 * @return array $data The data for the CSV file
	 */
	public function get_data() {
		global $wpdb, $edd_options;

		$data = array();
		
		if ( $_POST['month'] == 'all' ){
			
			$payments = edd_get_payments( array(
				'offset' => 0,
				'number' => -1,
				'mode'   => edd_is_test_mode() ? 'test' : 'live',
				'status' => isset( $_POST['edd_export_payment_status'] ) ? $_POST['edd_export_payment_status'] : 'any',
				'year'   => isset( $_POST['year'] ) ? absint( $_POST['year'] ) : date( 'Y' )
			) );
			
		}
		else{
			$payments = edd_get_payments( array(
				'offset' => 0,
				'number' => -1,
				'mode'   => edd_is_test_mode() ? 'test' : 'live',
				'status' => isset( $_POST['edd_export_payment_status'] ) ? $_POST['edd_export_payment_status'] : 'any',
				'month'  => isset( $_POST['month'] ) ? absint( $_POST['month'] ) : date( 'n' ),
				'year'   => isset( $_POST['year'] ) ? absint( $_POST['year'] ) : date( 'Y' )
			) );
		}
		
		//Set defaults for totals
		$overall_total = 0;
		$total_tax = 0;
		
		$selected_country =  isset( $_POST['edd_export_payment_country'] ) ? $_POST['edd_export_payment_country'] : '0';
		$selected_state = isset( $_POST['card_state'] ) ? $_POST['card_state'] : '0';
					
		foreach ( $payments as $payment ) {
			$payment_meta 	= edd_get_payment_meta( $payment->ID );
			$user_info 		= edd_get_payment_meta_user_info( $payment->ID );
			$downloads      = edd_get_payment_meta_cart_details( $payment->ID );
			$total          = edd_get_payment_amount( $payment->ID );
			$user_id        = isset( $user_info['id'] ) && $user_info['id'] != -1 ? $user_info['id'] : $user_info['email'];
			$products       = '';
			$skus			= '';
			
			//If our country matches
			if ( ( isset( $user_info['address']['country'] ) && $user_info['address']['country'] == $selected_country ) || ( isset( $payment_meta['country'] ) && $payment_meta['country'] == $selected_country ) || $selected_country == '0' || $selected_country == 'all' ){
								
				//If the state matches the selection or is 'all'
				if ( ( isset( $user_info['address']['state'] ) && $user_info['address']['state'] == $selected_state ) || ( isset( $payment_meta['state'] ) && $payment_meta['state'] == $selected_state ) || $selected_state == '0' || $selected_state == 'all' ){
				
					if ( $downloads ) {
						foreach ( $downloads as $key => $download ) {
							
							// Download ID
							$id = isset( $payment_meta['cart_details'] ) ? $download['id'] : $download;
		
							// If the download has variable prices, override the default price
							$price_override = isset( $payment_meta['cart_details'] ) ? $download['price'] : null;
		
							$price = edd_get_download_final_price( $id, $user_info, $price_override );
		
							// Display the Downoad Name
							$products .= get_the_title( $id ) . ' - ';
		
							if ( edd_use_skus() ) {
								$sku = edd_get_download_sku( $id );
		
								if ( ! empty( $sku ) )
									$skus .= $sku;
							}
		
							if ( isset( $downloads[ $key ]['item_number'] ) && isset( $downloads[ $key ]['item_number']['options'] ) ) {
								$price_options = $downloads[ $key ]['item_number']['options'];
		
								if ( isset( $price_options['price_id'] ) ) {
									$products .= edd_get_price_option_name( $id, $price_options['price_id'] ) . ' - ';
								}
							}
							$products .= html_entity_decode( edd_currency_filter( $price ) );
		
							if ( $key != ( count( $downloads ) -1 ) ) {
								$products .= ' / ';
		
								if( edd_use_skus() )
									$skus .= ' / ';
							}
						
						}
					}
		
					if ( is_numeric( $user_id ) ) {
						$user = get_userdata( $user_id );
					} else {
						$user = false;
					}
					
					//Set up country
					if ( isset( $user_info['address']['country'] ) ){
						$country = $user_info['address']['country'];
					}
					else if( isset( $payment_meta['country'] ) ){
						$country = $payment_meta['country'];
					}
					else{
						$country = NULL;	
					}
					//Set up state
					if ( isset( $user_info['address']['state'] ) ){
						$state = $user_info['address']['state'];
					}
					else if( isset( $payment_meta['state'] ) ){
						$state = $payment_meta['state'];
					}
					else{
						$state = NULL;	
					}
					//Set up city
					if ( isset( $user_info['address']['city'] ) ){
						$city = $user_info['address']['city'];
					}
					else if( isset( $payment_meta['city'] ) ){
						$city = $payment_meta['city'];
					}
					else{
						$city = NULL;	
					}
					
					//Totals:
					$overall_total = $overall_total + html_entity_decode( edd_format_amount( $total ) );
					$total_tax = $total_tax + html_entity_decode( edd_get_payment_tax( $payment->ID, $payment_meta ) );
							
					$data[] = array(
						'id'       => $payment->ID,
						'email'    => $payment_meta['email'],
						'first'    => $user_info['first_name'],
						'last'     => $user_info['last_name'],
						'products' => $products,
						'skus'     => $skus,
						'amount'   => html_entity_decode( edd_format_amount( $total ) ),
						'tax'      => html_entity_decode( edd_get_payment_tax( $payment->ID, $payment_meta ) ),
						'discount' => isset( $user_info['discount'] ) && $user_info['discount'] != 'none' ? $user_info['discount'] : __( 'none', 'edd-location-export' ),
						'gateway'  => edd_get_gateway_admin_label( get_post_meta( $payment->ID, '_edd_payment_gateway', true ) ),
						'key'      => $payment_meta['key'],
						'date'     => $payment->post_date,
						'user'     => $user ? $user->display_name : __( 'guest', 'edd-location-export' ),
						'status'   => edd_get_payment_status( $payment, true ),
						'country'  => $country,
						'state'    => $state,
						'city '    => $city,
					);
		
					if( !edd_use_skus() )
						unset( $data['skus'] );
				}
			}
		}
		
		//Add a row at the bottom for the total		
		$data[] = array(
			'id'       => __( 'Totals:', 'edd_location_export' ),
			'email'    => NULL,
			'first'    => NULL,
			'last'     => NULL,
			'products' => NULL,
			'skus'     => NULL,
			'amount'   => $overall_total,
			'tax'      => $total_tax,
			'discount' => NULL,
			'gateway'  => NULL,
			'key'      => NULL,
			'date'     => NULL,
			'user'     => NULL,
			'status'   => NULL,
			'country'  => NULL,
			'state'    => NULL,
			'city '    => NULL,
		);

		if( !edd_use_skus() )
			unset( $data['skus'] );

		$data = apply_filters( 'edd_export_get_data', $data );
		$data = apply_filters( 'edd_export_get_data_' . $this->export_type, $data );

		return $data;
	}
}