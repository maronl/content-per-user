<?php

/**
 * The Manager is the core plugin responsible for including and
 * instantiating all of the code that composes the plugin
 */

/**
 * The Manager is the core plugin responsible for including and
 * instantiating all of the code that composes the plugin.
 *
 * The Manager includes an instance to the Loader which is 
 * responsible for coordinating the hooks that exist within the plugin.
 *
 * It also maintains a reference to the plugin slug which can be used in
 * internationalization, and a reference to the current version of the plugin
 * so that we can easily update the version in a single place to provide
 * cache busting functionality when including scripts and styles.
 *
 * @since 1.0.0
 */
class Content_Per_User_Manager {

    /**
     * A reference to the loader class that coordinates the hooks and callbacks
     * throughout the plugin.
     *
     * @access protected
     * @var PLUGIN_CLASS_NAME_BASE_Loader $loader Manages hooks between the WordPress hooks and the callback functions.
     */
    protected $loader;

    /**
     * Represents the slug of the plugin that can be used throughout the plugin
     * for internationalization and other purposes.
     *
     * @access protected
     * @var string $plugin_slug The single, hyphenated string used to identify this plugin.
     */
    protected $plugin_slug;

    /**
     * Maintains the current version of the plugin so that we can use it throughout
     * the plugin.
     *
     * @access protected
     * @var string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Store the options set for the plugin (if there are) to be used as context in the admin e public side.
     *
     * @access protected
     * @var string $options The current options set for the plugin.
     */
    protected $options;

    /**
     * Instantiates the plugin by setting up the core properties and loading
     * all necessary dependencies and defining the hooks.
     *
     * The constructor will define both the plugin slug and the verison
     * attributes, but will also use internal functions to import all the
     * plugin dependencies, and will leverage the Single_Post_Meta_Loader for
     * registering the hooks and the callback functions used throughout the
     * plugin.
     */
    public function __construct() {


        $this->plugin_slug = 'content-per-user';
        $this->version = '1.0.1';
        $this->options = get_option( 'content-per-user-options' );

        $this->load_dependencies();
        $this->define_register_activation_hook();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }



    /**
     * Imports the Classes needed to make the plugin working.
     *
     * The Manager administration class defines all unique functionality for
     * introducing custom functionality into the WordPress dashboard.
     *
     * The Manager public class defines all unique functionality for
     * introducing custom functionality into the public side.
	 *	
     * The Loader is the class that will coordinate the hooks and callbacks
     * from WordPress and the plugin. This function instantiates and sets the reference to the
     * $loader class property.
     *
     * @access private
     */
    private function load_dependencies() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-content-per-user-config.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-content-per-user-model.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-content-per-user-manager-admin.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-content-per-user-manager-options.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-content-per-user-manager-public.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-content-per-user-theme-functions.php';

        require_once plugin_dir_path( __FILE__ ) . 'class-content-per-user-loader.php';
        $this->loader = new Content_Per_User_Loader();

    }

    /**
     * Defines the hooks and callback functions that are used for setting up the plugin stylesheets, scripts, logic
     * and the plugin's meta box.
     *
     * @access private
     */
    private function define_admin_hooks() {

        $data_model = Content_Per_User_Model::getInstance();
        $admin = new Content_Per_User_Manager_Admin( $this->version, $this->options, $data_model);
        $admin_options = new Content_Per_User_Manager_Options( $this->version, $this->options );

        $this->loader->add_action( 'admin_init', $admin, 'register_scripts' );
        $this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_init', $admin_options, 'register_scripts' );
        $this->loader->add_action( 'admin_enqueue_scripts', $admin_options, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $admin_options, 'add_plugin_options_page' );
        $this->loader->add_action( 'admin_menu', $admin, 'create_admin_menu' );
        $this->loader->add_action( 'admin_init', $admin_options, 'options_page_init' );
        $this->loader->add_action( 'init', $admin, 'load_textdomain' );
        $this->loader->add_action( 'add_meta_boxes',$admin , 'add_meta_box_content_per_user' );
        $this->loader->add_action( 'save_post', $admin, 'save_meta_box_content_per_user' );
        $this->loader->add_action( 'edit_user_profile', $admin, 'user_profile_content_per_user_section' );
        $this->loader->add_action( 'wp_ajax_suggest_content_per_user', $admin, 'suggest_content_per_user' );
        $this->loader->add_action( 'wp_ajax_add_request_per_content', $admin, 'add_request_per_content' );
        $this->loader->add_action( 'wp_ajax_nopriv_add_request_per_content', $admin, 'add_request_per_content' );
        $this->loader->add_action( 'wp_ajax_accept_request_per_content', $admin, 'accept_request_per_content' );
        $this->loader->add_action( 'wp_ajax_nopriv_accept_request_per_content', $admin, 'accept_request_per_content' );
        $this->loader->add_action( 'wp_ajax_refuse_request_per_content', $admin, 'refuse_request_per_content' );
        $this->loader->add_action( 'wp_ajax_nopriv_refuse_request_per_content', $admin, 'refuse_request_per_content' );
        $this->loader->add_action( 'wp_ajax_add_content_per_user', $admin, 'add_content_per_user' );
        $this->loader->add_action( 'wp_ajax_remove_content_per_user', $admin, 'remove_content_per_user' );

        $this->loader->add_action( 'admin_menu', $admin, 'add_content_request_menu_bubble' );

    }

    /**
     * Defines the hooks and callback functions that are used for rendering information on the front
     * end of the site.
     *
     * @access private
     */
    private function define_public_hooks() {

        $data_model = Content_Per_User_Model::getInstance();
        $public = new Content_Per_User_Manager_Public( $this->version, $this->options, $data_model);

        $this->loader->add_filter( 'the_content', $public, 'the_content_filter', 99 );
        $this->loader->add_action( 'init', $public, 'register_scripts' );
        $this->loader->add_action( 'wp_enqueue_scripts', $public, 'enqueue_scripts' );

        Content_Per_User_Theme_Functions::define_theme_functions();

    }

    private function define_register_activation_hook() {
        $data_model = Content_Per_User_Model::getInstance();
        $admin = new Content_Per_User_Manager_Admin( $this->version, $this->options, $data_model);
        register_activation_hook( dirname( dirname( __FILE__ ) ) . '\content-per-user.php' , array( $admin, 'install_db_structure' ) );
        register_activation_hook( dirname( dirname( __FILE__ ) ) . '\content-per-user.php' , array( $admin, 'create_roles' ) );
        register_deactivation_hook(dirname( dirname( __FILE__ ) ) . '\content-per-user.php' , array( $admin, 'deactivate_plugin' ) );
    }

    /**
     * Sets this class into motion.
     *
     * Executes the plugin by calling the run method of the loader class which will
     * register all of the hooks and callback functions used throughout the plugin
     * with WordPress.
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * Returns the current version of the plugin to the caller.
     *
     * @return string $this->version The current version of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}