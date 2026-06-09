<?php

/**
 * Class FlexCore_Server_Admin
 * Handles all admin-related functionality
 */
class FlexCore_Server_Admin {
    private $plugin_name;
    private $version;

    /**
     * Initialize the admin class
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Register the stylesheets for the admin area.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/flexcore-server-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/flexcore-server-admin.js',
            array('jquery'),
            $this->version,
            false
        );
    }

    /**
     * Add menu item to WordPress admin
     */
    public function add_admin_menu() {
        add_options_page(
            'FlexCore Server Settings',
            'FlexCore Server',
            'manage_options',
            'flexcore-server',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting('flexcore_server_settings', 'flexcore_api_base_url');
        register_setting('flexcore_server_settings', 'flexcore_token_storage', array(
            'type' => 'string',
            'default' => 'cookie'
        ));
        register_setting('flexcore_server_settings', 'flexcore_login_page');
        register_setting('flexcore_server_settings', 'flexcore_register_page');
        register_setting('flexcore_server_settings', 'flexcore_verify_otp_page');
        register_setting('flexcore_server_settings', 'flexcore_dashboard_page');
        register_setting('flexcore_server_settings', 'flexcore_profile_page');
        register_setting('flexcore_server_settings', 'flexcore_enable_redirects', array(
            'type' => 'boolean',
            'default' => true
        ));
    }

    /**
     * Render the settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('flexcore_server_settings');
                do_settings_sections('flexcore_server_settings');
                ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="flexcore_api_base_url">API Base URL</label>
                        </th>
                        <td>
                            <input type="url" id="flexcore_api_base_url" name="flexcore_api_base_url"
                                value="<?php echo esc_attr(get_option('flexcore_api_base_url', 'http://localhost:3000')); ?>"
                                class="regular-text">
                            <p class="description">Enter the base URL of your FlexCore API server (e.g., http://192.168.0.121)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="flexcore_token_storage">Token Storage</label>
                        </th>
                        <td>
                            <select id="flexcore_token_storage" name="flexcore_token_storage">
                                <option value="cookie" <?php selected(get_option('flexcore_token_storage', 'cookie'), 'cookie'); ?>>
                                    Secure Cookie (Recommended)
                                </option>
                                <option value="localstorage" <?php selected(get_option('flexcore_token_storage', 'cookie'), 'localstorage'); ?>>
                                    LocalStorage
                                </option>
                            </select>
                            <p class="description">Choose how to store authentication tokens</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="flexcore_login_page">Login Page</label>
                        </th>
                        <td>
                            <?php wp_dropdown_pages(array(
                                'name' => 'flexcore_login_page',
                                'selected' => get_option('flexcore_login_page'),
                                'show_option_none' => 'Select a page',
                            )); ?>
                            <p class="description">Select the page where the login form will be displayed</p>
                        </td>
                    </tr>
                     <tr>
                        <th scope="row">
                            <label for="flexcore_register_page">Register Page</label>
                        </th>
                        <td>
                            <?php wp_dropdown_pages(array(
                                'name' => 'flexcore_register_page',
                                'selected' => get_option('flexcore_register_page'),
                                'show_option_none' => 'Select a page',
                            )); ?>
                            <p class="description">Select the page where the register form will be displayed</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="flexcore_verify_otp_page">OTP Verification Page</label>
                        </th>
                        <td>
                            <?php wp_dropdown_pages(array(
                                'name' => 'flexcore_verify_otp_page',
                                'selected' => get_option('flexcore_verify_otp_page'),
                                'show_option_none' => 'Select a page',
                            )); ?>
                            <p class="description">Select the page for OTP verification after login</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="flexcore_dashboard_page">Dashboard Page</label>
                        </th>
                        <td>
                            <?php wp_dropdown_pages(array(
                                'name' => 'flexcore_dashboard_page',
                                'selected' => get_option('flexcore_dashboard_page'),
                                'show_option_none' => 'Select a page',
                            )); ?>
                            <p class="description">Select the page for the user dashboard</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="flexcore_profile_page">Profile Page</label>
                        </th>
                        <td>
                            <?php wp_dropdown_pages(array(
                                'name' => 'flexcore_profile_page',
                                'selected' => get_option('flexcore_profile_page'),
                                'show_option_none' => 'Select a page',
                            )); ?>
                            <p class="description">Select the page for user profile management</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="flexcore_enable_redirects">Enable Redirects</label>
                        </th>
                        <td>
                            <input type="checkbox" id="flexcore_enable_redirects" name="flexcore_enable_redirects" value="1"
                                <?php checked(get_option('flexcore_enable_redirects', true), true); ?>>
                            <p class="description">Automatically redirect users to appropriate pages based on their authentication status</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
