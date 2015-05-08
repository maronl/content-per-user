<?php

class Content_Per_User_Manager_Options {

    private $version;

    private $options;

    private $js_configuration;

    function __construct($version, $options) {
        $this->version = $version;
        $this->options = $options;
        if(WP_DEBUG == false) {
            $this->js_configuration['js_path'] = CONTENT_PER_USER_JS_PROD_PATH;
            $this->js_configuration['js_extension'] = $this->version . '.min.js';
        }else{
            $this->js_configuration['js_path'] = CONTENT_PER_USER_JS_PATH;
            $this->js_configuration['js_extension'] = 'js';
        }
    }

    public function register_scripts() {
        wp_register_script( 'content-per-user-options-js', plugins_url( $this->js_configuration['js_path'] . 'content-per-user-options.' . $this->js_configuration['js_extension'], __FILE__ ) );
    }

    public function enqueue_scripts($hook) {
        if( 'settings_page_content-per-user-options' == $hook ){
            wp_enqueue_script('content-per-user-options-js');
        }
    }

    function add_plugin_options_page() {
        add_options_page(
            'WP Content per User options',
            __('Content per User Options', 'content-per-user'),
            'manage_options',
            'content-per-user-options',
            array( $this, 'render_admin_options_page' )
        );
    }

    function render_admin_options_page() {
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php _e( 'Content per User options', 'content-per-user' )?></h2>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'content-per-user-options' );
                do_settings_sections( 'content-per-user-options' );
                submit_button();
                ?>
            </form>
        </div>
    <?php
    }

    function options_page_init() {
        register_setting(
            'content-per-user-options', // Option group
            'content-per-user-options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'content-per-user-options', // ID
            __('General settings', 'content-per-user'), // Title
            array( $this, 'print_section_info' ), // Callback
            'content-per-user-options' // Page
        );

        add_settings_field(
            'content-per-user-post-type',
            __( 'Post type', 'content-per-user' ),
            array( $this, 'post_type_callback' ),
            'content-per-user-options',
            'content-per-user-options'
        );

    }

    public function print_section_info()
    {
        //_e( 'Enter your settings below:', 'content-per-user' );
    }

    function sanitize( $input ) {

        foreach ($input as $key => $value){
            if( ! is_array( $value ) )
                $input[$key] =  sanitize_text_field($value);
        }

        if( $input['content-per-user-post-type'] )
            $input['content-per-user-post-type'] = implode( '|||', $input['content-per-user-post-type'] );

        return $input;
    }

    public function post_type_callback() {
        $disabled = ( isset( $this->options['content-per-user-entire-website'] ) && ( 1 == $this->options['content-per-user-entire-website'] ) ) ? 'disabled="disabled"' : '';

        $value = isset( $this->options['content-per-user-post-type'] ) ? esc_attr( $this->options['content-per-user-post-type']) : '';
        $selected_post_types = explode( '|||', $value );

        $post_types = get_post_types( array(), 'objects');

        unset($post_types['attachment']);
        unset($post_types['revision']);
        unset($post_types['nav_menu_item']);

        $format = '<br /><input type="checkbox" class="content-per-user-post-type" name="content-per-user-options[content-per-user-post-type][]" value="%s" %s %s/> %s';

        foreach( $post_types as $key => $value ){
            $checked = '';
            if( in_array( $key, $selected_post_types )) {
                $checked = 'checked';
            }

            printf( $format, $key, $checked, $disabled, $value->name );
        }

    }

}