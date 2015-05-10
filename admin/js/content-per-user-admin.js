//js code for admin
jQuery(function() {

    console.log('load admin js');

    jQuery( "#suggest-content-per-user" ).autocomplete({
        source: ajaxurl + '?action=suggest_content_per_user&user_id=' + content_per_user.a_value,
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
        newContent = '<span><a class="content-per-user-delbutton" id="content-per-user-check-num-'+postId+'">X</a>&nbsp;'+postTitle+'</span>';
        jQuery( '.contentperuserchecklist').append(newContent);
        jQuery('#content-per-user-post-id').val('');
        jQuery('#content-per-user-post-title').val('');
        jQuery('#suggest-content-per-user').val('');
    });

    jQuery( ".contentperuserchecklist" ).on( "click", ".content-per-user-delbutton", function() {
        jQuery('#' + this.id).parent().remove();
    });

});