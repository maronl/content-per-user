<?php
/**
 * The file responsible for starting the plugin
 *
 * WordPress plugin to manage content of a post (or custom post) in tabs
 * *
 * @wordpress-plugin
 * Plugin Name: Content per User
 * Plugin URI: https://github.com/maronl/content-per-user.git
 * Description: WordPress plugin to allow adminstrators define if a post, page or custom post is available for all users or only for specific users (defined by username)
 * Version: 1.0.0
 * Author: Luca Maroni
 * Author URI: http://maronl.it
 * Text Domain: content-per-user
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /langs
 */

// If this file is called directly, then abort execution.
if (!defined('WPINC')) {
    die;
}

/**
 * Include the core class responsible for loading all necessary components of the plugin.
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-content-per-user-manager.php';

/**
 * Instantiates the Manager class and then
 * calls its run method officially starting up the plugin.
 */
function run_content_per_user_manager()
{

    $cpu = new Content_Per_User_Manager();
    $cpu->run();

}

// Call the above function to begin execution of the plugin.
run_content_per_user_manager();