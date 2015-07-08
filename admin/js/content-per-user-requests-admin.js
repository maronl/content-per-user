//js code for admin
jQuery(function() {

    jQuery( ".cxu-list" ).on( "click", ".accept-request", function() {

        reqId = jQuery(this).data('req-id');

        var data = 'action=accept_request_per_content'
            + '&req_id=' + reqId;

        jQuery.post(ajaxurl, data, function(response) {
            if(response.status == 1){
                jQuery('#req_'+response.id).removeClass('pending');
                jQuery('#req_'+response.id).removeClass('refused');
                jQuery('#req_'+response.id).addClass('accepted');
                jQuery('#req_'+response.id+' .status').html(response.new_status);
                if(response.count_pending <= 0)
                    jQuery('.count-req-content').remove();
                else
                    jQuery('.count-req-content').html(response.count_pending);
                //jQuery('#req_'+response.id+' input').remove();
            }else{
                console.log(response.status);
                console.log(response.message);
            }
        },'json');

    });

    jQuery( ".cxu-list" ).on( "click", ".refuse-request", function() {

        reqId = jQuery(this).data('req-id');

        var data = 'action=refuse_request_per_content'
            + '&req_id=' + reqId;

        jQuery.post(ajaxurl, data, function(response) {
            if(response.status == 1){
                jQuery('#req_'+response.id).removeClass('pending');
                jQuery('#req_'+response.id).removeClass('accepted');
                jQuery('#req_'+response.id).addClass('refused');
                jQuery('#req_'+response.id+' .status').html(response.new_status);
                if(response.count_pending <= 0)
                    jQuery('.count-req-content').remove();
                else
                    jQuery('.count-req-content').html(response.count_pending);
                //jQuery('#req_'+response.id+' input').remove();
            }else{
                console.log(response.status);
                console.log(response.message);
            }
        },'json');

    });

});