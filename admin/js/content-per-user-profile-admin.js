//js code for admin
jQuery(function() {

    console.log('load admin js');

    jQuery( "#suggest-content-per-user" ).autocomplete({
        source: ajaxurl + '?action=suggest_content_per_user&user_id=' + content_per_user.user_id,
        minLength: 2,
        select: function( event, ui ) {
            jQuery('#content-per-user-post-id').val(ui.item.id);
            jQuery('#content-per-user-post-title').val(ui.item.value);
        }
    });

    jQuery( '#add-content-per-user').click( function(){
        postId = jQuery('#content-per-user-post-id').val();
        postTitle = jQuery('#content-per-user-post-title').val();
        if( postId == '' || postTitle == ''){
            return;
        }
        var data = 'action=add_content_per_user'
            + '&user_id=' + content_per_user.user_id
            + '&post_id=' + postId

        jQuery.post(ajaxurl, data, function(response) {
            if(response.status == 1){
                postId = jQuery('#content-per-user-post-id').val();
                postTitle = jQuery('#content-per-user-post-title').val();
                newContent = '<span><a class="content-per-user-delbutton" id="content-per-user-check-num-'+postId+'">X</a>&nbsp;'+postTitle+'</span>';
                jQuery( '.contentperuserchecklist').append(newContent);
                jQuery('#content-per-user-post-id').val('');
                jQuery('#content-per-user-post-title').val('');
                jQuery('#suggest-content-per-user').val('');
            }else{
                console.log(response.status);
                console.log(response.message);
            }
        },'json');

    });

    jQuery( ".contentperuserchecklist" ).on( "click", ".content-per-user-delbutton", function() {

        var data = 'action=remove_content_per_user'
            + '&user_id=' + content_per_user.user_id
            + '&post_id=' + this.id.split("-").pop();

        jQuery.post(ajaxurl, data, function(response) {
            if(response.status == 1){
                jQuery('#content-per-user-check-num-' + response.post_id).parent().remove();
            }else{
                console.log(response.status);
                console.log(response.message);
            }
        },'json');

    });

});