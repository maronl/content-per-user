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

    function install_db_structure() {

        global $wpdb;

        $table_name = $wpdb->prefix . 'content_per_user';

        $charset_collate = '';

        if ( ! empty( $wpdb->charset ) ) {
            $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        }

        if ( ! empty( $wpdb->collate ) ) {
            $charset_collate .= " COLLATE {$wpdb->collate}";
        }

        $sql = "CREATE TABLE $table_name (
          id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          post_id bigint(20) unsigned NOT NULL,
          user_id bigint(20) unsigned NOT NULL,
          manager_id bigint(20) unsigned NULL,
          status SMALLINT NOT NULL DEFAULT 0,
          created TIMESTAMP,
          modified TIMESTAMP,
          PRIMARY KEY (id),
          KEY cpu_post_id (post_id),
          KEY cpu_user_id (user_id)
	    ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        dbDelta( $sql );

        add_option( 'content_per_user_db_version', $this->version );

    }

    public function register_scripts($hook) {
        wp_register_script( 'content-per-user-profile-admin-js', plugins_url( $this->js_configuration['js_path'] . 'content-per-user-profile-admin.' . $this->js_configuration['js_extension'], __FILE__ ), array('jquery-ui-autocomplete') );
        wp_register_script( 'content-per-user-requests-admin-js', plugins_url( $this->js_configuration['js_path'] . 'content-per-user-requests-admin.' . $this->js_configuration['js_extension'], __FILE__ ), array('jquery-ui-autocomplete') );
    }

    public function enqueue_scripts($hook) {
        if( $hook == 'user-edit.php' ){

            if( isset( $_GET['user_id'] ) ){

                // Localize the script with user data
                $translation_array = array(
                    'user_id' => $_GET['user_id']
                );

                wp_localize_script( 'content-per-user-profile-admin-js', 'content_per_user', $translation_array );

                wp_enqueue_script( 'content-per-user-profile-admin-js' );

            }
        }

        if( $hook == 'toplevel_page_content-per-user/content-per-user-admin' ){

            wp_enqueue_script( 'content-per-user-requests-admin-js' );

        }
    }

    function create_admin_menu() {
        //add_menu_page( __('Content Request', 'content_per_user'), __('Content Request', 'content_per_user'), 'manage_content_per_user', 'content-per-user/content-per-user-admin.php', array($this, 'render_admin_page'), 'dashicons-megaphone', 26 );
        add_menu_page( __('Content Request', 'content_per_user'), __('Content Request', 'content_per_user'), 'manage_options', 'content-per-user/content-per-user-admin.php', array($this, 'render_admin_page'), 'dashicons-megaphone', 26 );
    }

    function render_admin_page() {

        if( isset( $_GET['req'] ) ){

            $req_info = $this->data_model->get_request_per_content( $_GET['req'] );

            include dirname(__FILE__) . '/partials/single-request.php';

        }else{

            $current_user_territory_id = get_user_meta( get_current_user_id(), 'salesforce_territoryID', true);

            $params = array();

            if(! empty($current_user_territory_id)){
                $params['territoryID'] = $current_user_territory_id;
            }

            $latest_requests = $this->data_model->get_requests_per_content($params);

            include dirname(__FILE__) . '/partials/admin-page.php';

        }


    }

    function add_content_request_menu_bubble() {
        global $wpdb;
        $current_user_territory_id = get_user_meta( get_current_user_id(), 'salesforce_territoryID', true);
        $params = array();
        if(! empty($current_user_territory_id)){
            $params['territoryID'] = $current_user_territory_id;
        }
        $pend_count = $this->data_model->count_pending_requests($params);
        if( ! $pend_count ){
            return;
        }

        global $menu;
        foreach ( $menu as $key => $value ) {
            if ( $menu[$key][2] == 'content-per-user/content-per-user-admin.php' ) {
                $menu[$key][0] .= " <span class='update-plugins count-$pend_count'><span class='plugin-count count-req-content'>" . $pend_count . '</span></span>';
                return;
            }
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

        $user_id = $_GET['user_id'];

        $current_content_per_user = $this->data_model->get_content_per_user( $user_id );

        ?>

        <h3><?php _e( 'Content per User', 'content-per-user' )?></h3>

        <div class="message-content-per-user"></div>
        <input type="hidden" id="content-per-user-post-id" value="" autocomplete="off">
        <input type="hidden" id="content-per-user-post-title" value="" autocomplete="off">

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

        <div class="tagchecklist contentperuserchecklist">
            <?php foreach( $current_content_per_user as $content){
                $format = '<span><a class="content-per-user-delbutton" id="content-per-user-check-num-%d">X</a>&nbsp;%s</span>';
                printf( $format, $content->id, $content->value);
            }
            ?>
        </div>


    <?php
    }

    function suggest_content_per_user() {

        $user_id = $_GET['user_id'];

        $term = $_GET['term'];

        $res = $this->data_model->suggest_content_per_user( $user_id, $term );

        echo json_encode($res);

        die;

    }

    function add_request_per_content() {

        $user_id = get_current_user_id();

        $post_id = $_POST['post_id'];

        $insert = $this->data_model->add_request_per_content( $user_id, $post_id );

        if($insert){
            $res = array(
                'status' => 1,
                'msg' => __('The request has been sent successfully!', 'content-per-user')
            );
        }else{
            $res = array(
                'status' => 0,
                'msg' => __('Error saving the data. Please try again!', 'content-per-user')
            );
        }

        echo json_encode($res);

        die;

    }

    function accept_request_per_content() {

        $req_id = $_POST['req_id'];

        $accept = $this->data_model->accept_request_per_content( $req_id );

        if($accept){
            $current_user_territory_id = get_user_meta( get_current_user_id(), 'salesforce_territoryID', true);
            $params = array();
            if(! empty($current_user_territory_id)){
                $params['territoryID'] = $current_user_territory_id;
            }

            $res = array(
                'status' => 1,
                'msg' => __('The request has been accepted!!', 'content-per-user'),
                'id' => $req_id,
                'new_status' => __('Accepted', 'content-per-user'),
                'count_pending' => $this->data_model->count_pending_requests($params)
            );
            $this->send_accepted_request_notificaiton( $req_id );
        }else{
            $res = array(
                'status' => 0,
                'msg' => __('Error saving the data. Please try again!', 'content-per-user'),
                'id' => $req_id
            );
        }

        echo json_encode($res);

        die;

    }

    function refuse_request_per_content() {

        $req_id = $_POST['req_id'];

        $refuse = $this->data_model->refuse_request_per_content( $req_id );

        if($refuse){
            $res = array(
                'status' => 1,
                'msg' => __('The request has been refused!!', 'content-per-user'),
                'id' => $req_id,
                'new_status' => __('Refused', 'content-per-user'),
                'count_pending' => $this->data_model->count_pending_requests()
            );
        }else{
            $res = array(
                'status' => 0,
                'msg' => __('Error saving the data. Please try again!', 'content-per-user'),
                'id' => $req_id
            );
        }

        echo json_encode($res);

        die;

    }

    function add_content_per_user() {

        $user_id = $_POST['user_id'];

        $post_id = $_POST['post_id'];

        $insert = $this->data_model->add_content_per_user( $user_id, $post_id );

        if($insert){
            $res = array(
                'status' => 1
            );
            $this->send_user_notification( $user_id, $post_id );
        }else{
            $res = array(
                'status' => 0,
                'msg' => __('Error saving the data. Please try again!', 'content-per-user')
            );
        }

        echo json_encode($res);

        die;

    }

    function remove_content_per_user() {

        $post_id = $_POST['post_id'];

        $user_id = $_POST['user_id'];

        $delete = $this->data_model->remove_content_per_user( $user_id, $post_id );

        if($delete){
            $res = array(
                'status' => 1,
                'post_id' => $post_id
            );
        }else{
            $res = array(
                'status' => 0,
                'msg' => __('Error deleting the data. Please try again!', 'content-per-user')
            );
        }

        echo json_encode($res);

        die;

    }

    function create_roles() {

        $new_role = add_role(
            'content_per_user_manager',
            __('Content x User Manager', 'content-per-user'),
            array( 'read' => true )
        );

        $new_role->add_cap('manage_content_per_user');

    }

    function deactivate_plugin() {

        remove_role('content_per_user_manager');

    }



    function load_textdomain() {

        load_plugin_textdomain( 'content-per-user', false, dirname( dirname( plugin_basename( __FILE__ ) ) )  . '/langs' );

    }

    private function send_accepted_request_notificaiton( $req_id ) {

        $req_info = $this->data_model->get_request_per_content( $req_id );

        $this->send_user_notification( $req_info->user_id, $req_info->post_id );

    }

    private function send_user_notification( $user_id, $post_id ) {

        $user = get_userdata( $user_id );

        $first_name = get_user_meta($user_id, 'first_name', true);

        $last_name = get_user_meta($user_id, 'last_name', true);

        $full_name = $first_name . ' ' . $last_name;

        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

        $post = get_post( $post_id );

        $permalink = get_permalink( $post_id );

        $message = sprintf( __('Egr. %s,', 'content-per-user'), $full_name ) . "\r\n\r\n";

        $message .= __("Il contenuto da lei richiesto è stato abilitato: ", 'content-per-user') . $post->post_title . "\r\n\r\n";

        $message .= __('Clicchi sul link qui sotto per accedere:', 'content-per-user') . "\r\n";

        $message .= $permalink . "\r\n\r\n";

        $message .= __('Per accedere ai contenuti dovrà essere connesso con le proprie credenziali.', 'content-per-user') . "\r\n\r\n";

        $message .= __('Le ricordiamo, inoltre, che per qualsiasi problema la nostra redazione è a sua disposizione.', 'content-per-user') . "\r\n\r\n";

        $message .= __('Cordiali saluti', 'content-per-user') . "\r\n";

        $message .= __('La redazione di Clouderma', 'content-per-user') . "\r\n";

        $headers = array('Bcc: redazione@clouderma.com');

        $headers[]  = 'Bcc: lcmaroni77@gmail.com';

        wp_mail( $user->user_email, sprintf( __('[%s] Conferma accesso al contenuto richiesto', 'content-per-user' ), $blogname ), $message, $headers );

    }

}