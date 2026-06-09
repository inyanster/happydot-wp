<?php
/**
 * The core plugin class
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class FlexCore_Server {
    protected $loader;
    protected $utils;
    protected $ajax_handler;
    protected $api;
    protected $session;
    protected $plugin_name;
    protected $version;
    private $plugin_base_dir;

    public function __construct() {
        $this->plugin_base_dir = dirname(dirname(__FILE__));
        
        if (defined('FLEXCORE_SERVER_VERSION')) {
            $this->version = FLEXCORE_SERVER_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'flexcore-server';

        // Load all dependencies immediately
        $this->load_dependencies();
        
        // Initialize components
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        // Load core classes
        require_once $this->plugin_base_dir . '/includes/class-flexcore-server-loader.php';
        require_once $this->plugin_base_dir . '/includes/class-flexcore-server-utils.php';
        require_once $this->plugin_base_dir . '/includes/class-flexcore-server-ajax-handler.php';
        require_once $this->plugin_base_dir . '/includes/class-flexcore-server-api.php';
        require_once $this->plugin_base_dir . '/includes/class-flexcore-server-session.php';
        require_once $this->plugin_base_dir . '/includes/class-flexcore-server-template-loader.php';
        require_once $this->plugin_base_dir . '/public/class-flexcore-server-public.php';
        require_once $this->plugin_base_dir . '/admin/class-flexcore-server-admin.php';

        // Initialize core components
        $this->loader = new FlexCore_Server_Loader();
        $this->utils = new FlexCore_Server_Utils();
        $this->ajax_handler = new FlexCore_Server_Ajax_Handler();
        $this->api = new FlexCore_Server_API();
        $this->session = new FlexCore_Server_Session();
    }

    private function set_locale() {
        if (function_exists('load_plugin_textdomain')) {
            load_plugin_textdomain(
                'flexcore-server',
                false,
                dirname(plugin_basename($this->plugin_base_dir)) . '/languages/'
            );
        }
    }

    private function define_admin_hooks() {
        $plugin_admin = new FlexCore_Server_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
    }

    private function define_public_hooks() {
        $plugin_public = new FlexCore_Server_Public();

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action('init', $plugin_public, 'init');

        // Start session for auth
        $this->loader->add_action('init', $this, 'start_session', 1);
        
        // Register AJAX handlers
        $this->loader->add_action('wp_ajax_flexcore_login', $this->ajax_handler, 'handle_login');
        $this->loader->add_action('wp_ajax_nopriv_flexcore_login', $this->ajax_handler, 'handle_login');
        $this->loader->add_action('wp_ajax_flexcore_verify_otp', $this->ajax_handler, 'handle_verify_otp');
        $this->loader->add_action('wp_ajax_nopriv_flexcore_verify_otp', $this->ajax_handler, 'handle_verify_otp');
        $this->loader->add_action('wp_ajax_flexcore_get_profile', $this->ajax_handler, 'handle_get_profile');
        $this->loader->add_action('wp_ajax_nopriv_flexcore_get_profile', $this->ajax_handler, 'handle_get_profile');
        $this->loader->add_action('wp_ajax_flexcore_update_profile', $this->ajax_handler, 'handle_update_profile');
        $this->loader->add_action('wp_ajax_nopriv_flexcore_update_profile', $this->ajax_handler, 'handle_update_profile');
        $this->loader->add_action('wp_ajax_flexcore_delete_account', $this->ajax_handler, 'handle_delete_account');
        $this->loader->add_action('wp_ajax_nopriv_flexcore_delete_account', $this->ajax_handler, 'handle_delete_account');
        $this->loader->add_action('wp_ajax_flexcore_logout', $this->ajax_handler, 'handle_logout');
        $this->loader->add_action('wp_ajax_nopriv_flexcore_logout', $this->ajax_handler, 'handle_logout');
        $this->loader->add_action('wp_ajax_flexcore_change_password', $this->ajax_handler, 'handle_change_password');
        $this->loader->add_action('wp_ajax_nopriv_flexcore_change_password', $this->ajax_handler, 'handle_change_password');
        
        
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_version() {
        return $this->version;
    }

    public function start_session() {
        if (!session_id() && !headers_sent()) {
            session_start();
        }
    }
}
