<style>
    .cxu-wrapper{
        margin-right: 10px;
    }
    .cxu-filters{
        background-color: #fff;
        padding: 10px
    }
    .cxu-list{
        background-color: #fff;
        margin-bottom: 10px;
    }
    .cxu-item{
        background-color: #f9f9f9;
        width: auto;
        padding: 10px;
        border-bottom: 1px solid #eee;
        min-height: 60px;
    }
    .cxu-item .avatar{
        float: left;
        margin-right: 10px;
        margin-bottom: 10px;
    }
    .cxu-item .date{
        font-weight: bold;
    }
    .cxu-item input.button, .cxu-item input.button-primary{
        float: right;
        margin-right: 10px;
    }
    .cxu-item span.username {
        font-weight: bold;
    }
    .cxu-item span.status {
        margin-right: 10px;
        font-weight: bold;
    }
    .cxu-item.pending{
        background-color: #fffad6;
    }
    .cxu-item.accepted{
        background-color: #f9f9f9;
    }
    .cxu-item.refused{
        background-color: #fcccba;
    }
    .req-search{
        float: right;
    }
    .cxu-item.accepted input.accept-request{
        display:none;
    }
    .cxu-item.refused input.refuse-request{
        display:none;
    }

</style>
<div class="cxu-wrapper">
    <h1>Content per User Admin</h1>
    <div class="cxu-filters">
        <a href="#">tutti</a> - <a href="#">pending</a>
        <div class="req-search">
            Search: <input name="req-search">
        </div>
    </div>
    <div class="cxu-list">
        <?php foreach( $latest_requests as $req ) : ?>

            <?php
            $req_status = array(
                __('pending', 'content-per-user'),
                __('accepted', 'content-per-user'),
                __('refused', 'content-per-user')
            );
            $req_status_css = array('pending', 'accepted', 'refused');
            $req_avatar = get_avatar($req->user_id,50);
            $req_username = $req->first_name . ' ' . $req->last_name;
            if( empty( $req_username ) || $req_username == ' '   ) $req_username = $req->user_email;
            $req_date = $req->created;
            $req_post_title = $req->post_title;
            $req_post_url = get_permalink($req->post_id);
            ?>

            <div id="req_<?php echo $req->id; ?>" class="cxu-item <?php echo $req_status_css[$req->status]?>">
                <?php echo $req_avatar; ?>
                <input type="button" class="button button-primary accept-request" value="Abilita" data-req-id="<?php echo $req->id; ?>">
                <input type="button" class="button refuse-request" value="Nega" data-req-id="<?php echo $req->id; ?>">
                <p><span class="date"><?php echo $req_date; ?></span>
                    - <span class="username"><?php echo $req_username; ?></span>
                    - <span class="status"><?php echo $req_status[$req->status  ]?></span>
                    <a href="<?php echo $req_post_url; ?>" target="_blank"><?php echo $req_post_title; ?></a>
                </p>
            </div>

        <?php endforeach; ?>
    </div>
</div>