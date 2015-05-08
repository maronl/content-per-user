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

}