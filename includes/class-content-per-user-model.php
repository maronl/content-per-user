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
} 