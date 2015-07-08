<div class="jumbotron">
    <h1><?php _e('Restricted Access', 'content-per-user'); ?></h1>
    <?php if( is_user_logged_in() && cxu_check_request_per_content( get_current_user_id(), get_the_ID() ) ) :?>
        <p><?php _e('The request for the full article access have been already sent. it will be processed shortly!', 'content-per-user'); ?></p>
    <?php elseif( is_user_logged_in() ) :?>
        <div class="form-group">
            <input type="button" class="btn btn-primary request-content-access" data-id="<?php echo get_the_ID(); ?>" value="<?php _e('Send request to access content now!', 'content-per-user'); ?>">
        </div>
        <div id="request-content-access-feedback"></div>
    <?php else: ?>
        <p><?php _e('To request the full article access you have to be registered', 'content-per-user'); ?></p>
    <?php endif; ?>
</div>