<?php
/**
 * Retrieve a state's drop down
 *
 * @since 1.0
 * @return void
 */
function edd_location_export_ajax_get_states_field() {
	if( empty( $_POST['country'] ) ) {
		$_POST['country'] = edd_get_shop_country();
	}
	$states = edd_get_shop_states( $_POST['country'] );

	if( ! empty( $states ) ) {

		$args = array(
			'name'    => $_POST['field_name'],
			'options' => edd_get_shop_states( $_POST['country'] ),
			'show_option_all'  => _x( 'All', 'all dropdown items', 'edd-location-export' ),
			'show_option_none' => false
		);

		$response = EDD()->html->select( $args );

	} else {

		$response = 'nostates';
	}

	echo $response;

	edd_die();
}
add_action( 'wp_ajax_edd_location_export_get_shop_states', 'edd_location_export_ajax_get_states_field' );
add_action( 'wp_ajax_edd_location_export_get_shop_states', 'edd_location_export_ajax_get_states_field' );