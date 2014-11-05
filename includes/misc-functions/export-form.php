<?php
/**
 * Form which appears on the Downloads > Reports > Export Page
 *
 * @since 1.0
 * @return void
 */
function edd_location_export_form(){
		
	?>
	<div class="postbox">
		<h3><span><?php _e('Export Payment History By Location', 'edd-location-export'); ?></span></h3>
		<div class="inside">
			<p><?php _e( 'Download a CSV of all payments recorded by location.', 'edd-location-export' ); ?></p>
			<p>
				<form method="post">
					<?php echo EDD()->html->year_dropdown(); ?>
					<?php echo edd_location_export_month_dropdown(); ?>
					
					<select name="edd_export_payment_status">
						<option value="0"><?php _e( 'All Statuses', 'edd-location-export' ); ?></option>
						<?php
						$statuses = apply_filters( 'edd_payment_status_type_for_logs', edd_get_payment_statuses() );
						foreach( $statuses as $status => $label ) {
							echo '<option value="' . $status . '">' . $label . '</option>';
						}
						?>
					</select>
					
					<select id="edd_export_payment_country" name="edd_export_payment_country">
						<option value="0"><?php _e( 'All Countries', 'edd-location-export' ); ?></option>
						<?php
						$countries = apply_filters( 'edd_payment_country_for_logs', edd_get_country_list() );
						foreach( $countries as $country_code => $country ) {
						  echo '<option value="' . $country_code . '">' . $country . '</option>';
						}
						?>
					</select>
					
					<select id="card_state" name="card_state">
						<option value="0"><?php _e( 'Select Country First', 'edd-location-export' ); ?></option>
					</select>
					
					<input type="hidden" name="edd-action" value="payments_by_location_export"/>
					<input type="submit" value="<?php _e( 'Generate CSV', 'edd-location-export' ); ?>" class="button-secondary"/>
				</form>
			</p>
		</div><!-- .inside -->
	</div><!-- .postbox -->
	<?php
					
}
add_action( 'edd_reports_tab_export_content_top', 'edd_location_export_form' );