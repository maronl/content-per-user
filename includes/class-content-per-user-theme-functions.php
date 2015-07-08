<?php

class Content_Per_User_Theme_Functions {

    function __construct() { }

    public static function  define_theme_functions() {

	   if( ! function_exists( 'lps_get_related_posts' ) ) {
            function cxu_check_request_per_content( $user_id, $post ) {
                $cxu_data_model = Content_Per_User_Model::getInstance();
                return $cxu_data_model->check_request_per_content( $user_id, $post );
            }
        }

    }
} 