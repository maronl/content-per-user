<?php

class Content_Per_User_Manager_Public {

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

    function the_content_filter( $content ) {
        if ( is_single() && ! $this->data_model->user_can_access_post( get_current_user_id(), get_the_ID() )){
            $check_preview = strpos( $content, '<!--cpu-preview-->' );
            if( $check_preview === false ) {
                $content = '';
            }else{
                $content = substr($content, 0, $check_preview);
            }
            ob_start();
            if( file_exists( get_template_directory() . '/cpu-access-forbidden.php' ) ){
                include get_template_directory() . '/cpu-access-forbidden.php';
            }else {
                include 'partials/access-forbidden.php';
            }
            $out = ob_get_contents();
            ob_end_clean();
            $content .=  $out;
        }
        return $content;
    }
}