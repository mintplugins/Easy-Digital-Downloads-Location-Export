jQuery(document).ready(function($) {
    var $body = $('body'),
        $edd_cart_amount = $('.edd_cart_amount');

    // Update state/province field on checkout page
    $body.on('change', '#edd_export_payment_country', function() {
    
		var $this = $(this);
        if( 'card_state' != $this.attr('id') ) {

            // If the country field has changed, we need to update the state/provice field
            var postData = {
                action: 'edd_location_export_get_shop_states',
                country: $this.val(),
                field_name: 'card_state'
            };
			
            $.ajax({
                type: "POST",
                data: postData,
                url: edd_global_vars.ajaxurl,
                success: function (response) {
                    if( 'nostates' == response ) {
                        var text_field = '<input type="text" id="card_state" name="card_state" class="cart-state edd-input required" value="" placeholder="State/Provice" />';
                        $this.parent().find('#card_state').replaceWith( text_field );
                    } else {
						$this.parent().find('#card_state').replaceWith( response );
                    }
                }
            }).fail(function (data) {
                console.log(data);
            });
        }

        return false;
    });
});