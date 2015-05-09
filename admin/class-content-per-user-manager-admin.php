<?php

class Content_Per_User_Manager_Admin {

    private $version;

    private $options;

    private $data_model;

    private $js_configuration;

    function __construct( $version, $options, $data_model ) {
        $this->version = $version;
        $this->options = $options;
        $this->data_model = $data_model;
        $this->js_configuration = array();
        if(WP_DEBUG == false) {
            $this->js_configuration['js_path'] = CONTENT_PER_USER_JS_PROD_PATH;
            $this->js_configuration['js_extension'] = $this->version . '.min.js';
        }else{
            $this->js_configuration['js_path'] = CONTENT_PER_USER_JS_PATH;
            $this->js_configuration['js_extension'] = 'js';
        }
    }

    function add_meta_box_content_per_user() {

        $enabled_post_type =  $this->options['content-per-user-post-type'];

        $enabled_post_type = explode( '|||', $enabled_post_type );

        foreach ($enabled_post_type as $post_type){
            add_meta_box('content_per_user',
                __("Content per User", 'content-per-user'),
                array($this, 'render_meta_box_content_per_user'),
                $post_type ,
                'side'
            );
        }

    }

    function render_meta_box_content_per_user( $post ) {

        global $post;

        $value = get_post_meta( $post->ID, 'content-per-user', true );

        echo '<select name="content-per-user" class="large-text" autocomplete="off">';
        echo '<option value="" '.selected( '', $value, false).'>'.__('disabled', 'content-per-user').'</option>';
        echo '<option value="1" '.selected( '1', $value, false).'>'.__('enable', 'content-per-user').'</option>';
        echo '</select>';

        echo '<p>' . __( 'Select "enable" to restrict the access only to specific users', 'content-per-user' ) . '</p>';

    }

    function save_meta_box_content_per_user( $post_id ) {

        if ( ! isset( $_POST['content-per-user'] ) )
            return $post_id;

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;

        if ( ! current_user_can( 'edit_post', $post_id ) )
            return $post_id;

        $mydata = sanitize_text_field( $_POST['content-per-user'] );

        if( empty( $mydata ) ){
            delete_post_meta( $post_id, 'content-per-user' );
        }else{
            update_post_meta( $post_id, 'content-per-user', $mydata );
        }

    }

    function user_profile_content_per_user_section(){

        ?>

        <h3><?php _e( 'Content per User', 'content-per-user' )?></h3>

        <div class="message-content-per-user"></div>

        <table class="form-table">
            <tbody>
            <tr class="form-field form-required">
                <td scope="row"><label for="suggest-content-per-user"><?php _e('Select content using title or page slug','content-per-user'); ?></label>
                    <input type="text" value="" class="wp-suggest-user ui-autocomplete-input" id="suggest-content-per-user" name="suggest-content-per-user" autocomplete="off">
                    <input type="button" value="<?php _e('Add Content','content-per-user'); ?>" class="button button-primary" id="add-content-per-user" name="add-content-per-user">
                </td>
            </tr>
            </tbody>
        </table>

        <div class="tagchecklist">
            <span><a class="ntdelbutton" id="post_tag-check-num-0">X</a>&nbsp;prova</span>
            <span><a class="ntdelbutton" id="post_tag-check-num-1">X</a>&nbsp;pippo</span>
        </div>


    <?php
    }

    function load_textdomain() {
        load_plugin_textdomain( 'content-per-user', false, dirname( dirname( plugin_basename( __FILE__ ) ) )  . '/langs' );
    }

}