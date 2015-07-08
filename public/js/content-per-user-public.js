//js code for public
jQuery( document ).ready(function() {
    console.log( "ready!" );
    jQuery('input.request-content-access').click( function(){
        console.log('ciao');
        postId = jQuery(this).data('id');
        if( postId == ''){
            return;
        }
        var data = 'action=add_request_per_content'
            + '&post_id=' + postId;

        jQuery.post(ajaxurl, data, function(response) {
            if(response.status == 1){
                feedback = '<div class="alert alert-success">' + response.msg + '</div>';
                jQuery('input.request-content-access').remove();
            }else{
                feedback = '<div class="alert alert-danger">' + response.msg + '</div>';
            }
            jQuery('#request-content-access-feedback').html(feedback);
        },'json');

    });

});