<?php
class Content_Per_User_Model {

    private static $_instance = null;

    private function __construct() { }
    private function  __clone() { }

    public static function getInstance() {
        if( !is_object(self::$_instance) )
            self::$_instance = new Content_Per_User_Model();
        return self::$_instance;
    }

    public function add_request_per_content( $user_id = null, $post_id = null){

        global $wpdb;

        $table = $wpdb->prefix . "content_per_user";

        // no valid data
        if( is_null( $user_id ) || is_null( $post_id ) ){
            return false;
        }

        // request exist or content already enabled
        if( $this->check_request_per_content( $user_id, $post_id ) ){
            return false;
        }

        // add request
        $res = $wpdb->insert(
            $table,
            array(
                'user_id' => $user_id,
                'post_id' => $post_id,
                'manager_id' => null,
                'status' => 0,
                'created' => time(),
                'modified' => time()
            ),
            array(
                '%d',
                '%d',
                '%d',
                '%d',
                '%d',
                '%d'
            )
        );

        if($res){

            return $wpdb->insert_id;

        }
        return $res;

    }

    public function accept_request_per_content( $req_id = null ){

        global $wpdb;

        $table = $wpdb->prefix . "content_per_user";

        // no valid data
        if( is_null( $req_id ) ){
            return false;
        }

        // request exist or content already enabled
        if( $this->check_request_per_content_by_ID( $req_id ) ){
            return false;
        }

        //update request
        $res = $wpdb->update(
            $table,
            array(
                'status' => 1,
                'manager_id' => get_current_user_id(),
                'modified' => time()
            ),
            array( 'id' => $req_id ),
            array(
                '%d',
                '%d',
                '%d'
            ),
            array( '%d' )
        );

        return $res;

    }


    public function refuse_request_per_content( $req_id = null ){

        global $wpdb;

        $table = $wpdb->prefix . "content_per_user";

        // no valid data
        if( is_null( $req_id ) ){
            return false;
        }

        // request exist or content already enabled
        if( $this->check_request_per_content_by_ID( $req_id ) ){
            return false;
        }

        //update request
        $res = $wpdb->update(
            $table,
            array(
                'status' => 2,
                'manager_id' => get_current_user_id(),
                'modified' => time()
            ),
            array( 'id' => $req_id ),
            array(
                '%d',
                '%d',
                '%d'
            ),
            array( '%d' )
        );

        return $res;

    }


    public function get_requests_per_content( $params = array() ){

        global $wpdb;

        $table = $wpdb->prefix . "content_per_user";

        $query = $wpdb->prepare("SELECT c.*, u.user_email, fn.meta_value as first_name, ln.meta_value as last_name, p.post_title, p.post_name
 FROM " . $table . " as c
 LEFT JOIN " . $wpdb->users . " as u ON c.user_id= u.ID
 LEFT JOIN " . $wpdb->usermeta . " as fn ON c.user_id = fn.user_id and fn.meta_key = 'first_name'
 LEFT JOIN " . $wpdb->usermeta . " as ln ON c.user_id = ln.user_id and fn.meta_key = 'last_name'
 LEFT JOIN " . $wpdb->posts . " as p ON c.post_id = p.ID");

        $res = $wpdb->get_results($query);

        return $res;

    }

    public function get_request_per_content( $req_id ){

        global $wpdb;

        $table = $wpdb->prefix . "content_per_user";

        $query = $wpdb->prepare("SELECT c.*, u.user_email, fn.meta_value as first_name, ln.meta_value as last_name, p.post_title, p.post_name
 FROM " . $table . " as c
 LEFT JOIN " . $wpdb->users . " as u ON c.user_id= u.ID
 LEFT JOIN " . $wpdb->usermeta . " as fn ON c.user_id = fn.user_id and fn.meta_key = 'first_name'
 LEFT JOIN " . $wpdb->usermeta . " as ln ON c.user_id = ln.user_id and fn.meta_key = 'last_name'
 LEFT JOIN " . $wpdb->posts . " as p ON c.post_id = p.ID
 WHERE c.id = %d", $req_id );

        $res = $wpdb->get_row($query);

        return $res;

    }

    public function get_content_per_user( $user_id, $term = null){

        global $wpdb;

        $query = "select " . $wpdb->prefix . "posts.ID as id, " . $wpdb->prefix . "posts.post_title as value
          from " . $wpdb->prefix . "posts
          inner join " . $wpdb->prefix . "postmeta on " . $wpdb->prefix . "posts.ID = " . $wpdb->prefix . "postmeta.post_id and " . $wpdb->prefix . "postmeta.meta_key = 'content-per-user' and " . $wpdb->prefix . "postmeta.meta_value = 1
          left join " . $wpdb->prefix . "content_per_user on " . $wpdb->prefix . "content_per_user.post_id = " . $wpdb->prefix . "posts.ID and " . $wpdb->prefix . "content_per_user.user_id = %d
          where " . $wpdb->prefix . "content_per_user.user_id = %d";

        $params_query = array($user_id, $user_id);

        if( ! empty( $term ) ){
            $query .= " and " . $wpdb->prefix . "posts.post_title like '%%%s%%'";
            $params[] = $term;
        }

        $query = $wpdb->prepare($query, $params_query);

        $res = $wpdb->get_results($query);

        return $res;

    }

    public function suggest_content_per_user( $user_id, $term ){

        global $wpdb;

        $query = "select " . $wpdb->prefix . "posts.ID as id, " . $wpdb->prefix . "posts.post_title as value
          from " . $wpdb->prefix . "posts
          inner join " . $wpdb->prefix . "postmeta on " . $wpdb->prefix . "posts.ID = " . $wpdb->prefix . "postmeta.post_id and " . $wpdb->prefix . "postmeta.meta_key = 'content-per-user' and " . $wpdb->prefix . "postmeta.meta_value = 1
          left join " . $wpdb->prefix . "content_per_user on " . $wpdb->prefix . "content_per_user.post_id = " . $wpdb->prefix . "posts.ID and " . $wpdb->prefix . "content_per_user.user_id = %d
          where " . $wpdb->prefix . "content_per_user.user_id is NULL
          and " . $wpdb->prefix . "posts.post_title like '%%%s%%'";

        $query = $wpdb->prepare($query, $user_id, $term);

        $res = $wpdb->get_results($query);

        return $res;

    }

    public function add_content_per_user( $user_id, $post_id ){

        global $wpdb;

        $table = $wpdb->prefix . "content_per_user";

        $insert = $wpdb->insert(
            $table,
            array(
                'post_id' => $post_id,
                'user_id' => $user_id
            ),
            array(
                '%d',
                '%d'
            )
        );

        return $insert;

    }

    public function remove_content_per_user( $user_id, $post_id ){

        global $wpdb;

        $table = $wpdb->prefix . "content_per_user";

        $delete = $wpdb->delete(
            $table,
            array(
                'post_id' => $post_id,
                'user_id' => $user_id
            ),
            array(
                '%d',
                '%d'
            ) );

        return $delete;

    }

    function user_can_access_post( $user_id = null, $post_id = null ){
        global $wpdb;
        if( is_null( $user_id ) || is_null( $post_id ) ) {
            return false;
        }
        // administrator and editor can access everything
        if(current_user_can('edit_others_posts') || current_user_can('manage_options')){
            return true;
        }

        $post_settings = get_post_meta( $post_id, 'content-per-user', true);
        if( !$post_settings || empty($post_settings) ){
            return true;
        }

        $check = $wpdb->get_row(
            $wpdb->prepare(
                "
                 SELECT * FROM " . $wpdb->base_prefix . "content_per_user
                 WHERE post_id = %d
                 AND user_id = %d
                 AND status = 1
                ",
                $post_id, $user_id
            ), ARRAY_A
        );

        if( is_null( $check ) ){
            if(has_filter('content_per_user_user_can_access_post'))
                $check = apply_filters( 'content_per_user_user_can_access_post', $user_id, $post_id );
            if( is_null( $check ) ){
                return false;
            }
        }

        return $check;
    }

    function check_request_per_content( $user_id = null, $post_id = null ){

        global $wpdb;

        $table = $wpdb->base_prefix . "content_per_user";

        if( is_null( $user_id ) || is_null( $post_id ) ){
            return false;
        }

        $user_count = $wpdb->get_var( $wpdb->prepare("
          SELECT COUNT(*)
          FROM " . $table . "
          WHERE user_id = %d and post_id = %d
          and (status = 0 or status = 1)
          ", $user_id, $post_id ) );

        return ($user_count >= 1);
    }

    function check_request_per_content_by_ID( $req_id = null ){

        global $wpdb;

        $table = $wpdb->base_prefix . "content_per_user";

        if( is_null( $req_id ) ){
            return false;
        }

        $user_count = $wpdb->get_var( $wpdb->prepare("
          SELECT COUNT(*)
          FROM " . $table . "
          WHERE id = %d )
          ", $req_id ) );

        return ($user_count >= 1);
    }


    function count_pending_requests( $params = array() ){

        global $wpdb;

        $table = $wpdb->base_prefix . "content_per_user";

        $user_count = $wpdb->get_var( "
          SELECT COUNT(*)
          FROM " . $table . "
          WHERE status = 0
          " );

        return $user_count;
    }

} 