<?php

/**
 * FlexCore Server
 *
 * @package     FlexCore_Server
 * @author      Your Name
 * @copyright   2024 Your Name or Company Name
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: FlexCore Server
 * Plugin URI:  https://example.com/flexcore-server
 * Description: Integration with FlexCore authentication server providing login, registration, and user management features.
 * Version:     1.0.0
 * Author:      Your Name
 * Author URI:  https://example.com
 * Text Domain: flexcore-server
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 */
define('FLEXCORE_SERVER_VERSION', '1.0.2');

/**
 * The code that runs during plugin activation.
 */
function activate_flexcore_server()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-flexcore-server-activator.php';
    FlexCore_Server_Activator::activate();
    create_onemap_token_table();
}
/**
 * Create wp_onemap_token table for storing OneMap API tokens.
 */
function create_onemap_token_table()
{
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'onemap_token';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        access_token TEXT NOT NULL,
        expiry_timestamp BIGINT NOT NULL,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
/**
 * The code that runs during plugin deactivation.
 */
function deactivate_flexcore_server()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-flexcore-server-deactivator.php';
    FlexCore_Server_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_flexcore_server');
register_deactivation_hook(__FILE__, 'deactivate_flexcore_server');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-flexcore-server.php';

/**
 * Begins execution of the plugin.
 */
function run_flexcore_server()
{
    $plugin = new FlexCore_Server();
    $plugin->run();
}
// Initialize right after plugins are loaded, without waiting for init
add_action('plugins_loaded', 'run_flexcore_server');

add_action('init', function () {
    // Register custom post type for referral history
   error_log("Hello from init");
});

add_action('template_redirect', function () {
    $pagename = get_query_var('pagename');
    
    error_log('Current page: ' . $pagename);
   
    if(!FlexCore_Server_Session::is_authenticated() && ($pagename == 'point-history')){
        wp_redirect(site_url('login-page'));
        exit;}
    // Check if user is not authenticated
    if (!FlexCore_Server_Session::is_authenticated() && 
        ($pagename == 'happy-deals/refer-a-friend' || 
         $pagename == 'refer-a-friend-complete-your-account' || 
         $pagename == 'refer-a-friend-how-it-works')) {
        
        wp_redirect(home_url('/happy-deals/refer-a-friend/'));
        exit;
    }
    $membership_status = FlexCore_Server_Session::get_user_membership_status();
    
  if ($pagename == 'refer-a-friend-complete-your-account' && $membership_status == "4"
        ) {
        
        wp_redirect(home_url('/refer-a-friend-how-it-works'));
        exit;
    }
 elseif($pagename == 'refer-a-friend' && FlexCore_Server_Session::is_authenticated() &&
       $membership_status == "4") {
        
        wp_redirect(home_url('/refer-a-friend-how-it-works'));
        exit;
    }
    elseif($pagename == 'refer-a-friend' && FlexCore_Server_Session::is_authenticated() &&
        $membership_status != "4") {
        
        wp_redirect(home_url('/refer-a-friend-complete-your-account'));
        exit;
    }
    else if ($pagename == 'refer-a-friend-how-it-works' && 
        FlexCore_Server_Session::is_authenticated() && 
        $membership_status != "4") {
        
        wp_redirect(home_url('/refer-a-friend-complete-your-account'));
        exit;
    }
    
    if($pagename == 'my-dashboard' && FlexCore_Server_Session::is_authenticated()){
        wp_redirect(home_url('/my-account'));
        exit;
    }
    // If user is on 'refer-a-friend-complete-your-account' and profile is completed and survey taken, redirect
    // if ($pagename == 'refer-a-friend-complete-your-account' && 
    //     !empty($profile_data['metaData']['isProfileCompleted']) && 
    //     !empty($profile_data['metaData']['isSurveyTaken'])) {
        
    //     wp_redirect(home_url('/refer-a-friend-how-it-works'));
    //     exit;
    // }

    // If user is on 'refer-a-friend-how-it-works' and profile is not completed, redirect to 'refer-a-friend-complete-your-account'
    // elseif ($pagename == 'refer-a-friend-how-it-works' && 
    //     (empty($profile_data['metaData']['isProfileCompleted']) || !empty($profile_data['metaData']['isSurveyTaken']))) {
        
    //     wp_redirect(home_url('/refer-a-friend-complete-your-account'));
    //     exit;
    // }
    // elseif($pagename == 'refer-a-friend' && 
    //     !empty($profile_data['metaData']['isProfileCompleted']) && 
    //     !empty($profile_data['metaData']['isSurveyTaken'])) {
        
    //     wp_redirect(home_url('/refer-a-friend-how-it-works'));
    //     exit;
    // }
    // else if($pagename == 'refer-a-friend' && 
    //     empty($profile_data['metaData']['isProfileCompleted']) && 
    //     empty($profile_data['metaData']['isSurveyTaken'])) {
        
    //     wp_redirect(home_url('/refer-a-friend-complete-your-account'));
    //     exit;
    // }
});
