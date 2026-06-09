<?php

/**
 * Fired during plugin activation
 */
class FlexCore_Server_Activator {

    /**
     * Create necessary pages and set up default options
     */
    public static function activate() {
        // Create required pages if they don't exist
        $pages = array(
            'flexcore-login' => array(
                'title' => 'Login',
                'content' => '[flexcore_login]',
                'option' => 'flexcore_login_page'
            ),
            'flexcore-verify-otp' => array(
                'title' => 'Verify OTP',
                'content' => '[flexcore_verify_otp]',
                'option' => 'flexcore_verify_otp_page'
            ),
            'flexcore-dashboard' => array(
                'title' => 'Dashboard',
                'content' => '[flexcore_dashboard]',
                'option' => 'flexcore_dashboard_page'
            ),
            'flexcore-profile' => array(
                'title' => 'Profile',
                'content' => '[flexcore_profile]',
                'option' => 'flexcore_profile_page'
            ),
            'flexcore-forgot-password' => array(
                'title' => 'Forgot Password',
                'content' => '[flexcore_forgot_password]',
                'option' => 'flexcore_forgot_password_page'
            ),
            'flexcore-reset-password' => array(
                'title' => 'Reset Password',
                'content' => '[flexcore_reset_password]',
                'option' => 'flexcore_reset_password_page'
            ),
            'flexcore-delete-account' => array(
                'title' => 'Delete Account',
                'content' => '[flexcore_delete_account]',
                'option' => 'flexcore_delete_account_page'
            ),
            'flexcore-change-password' => array(
                'title' => 'Change Password',
                'content' => '[flexcore_change_password]',
                'option' => 'flexcore_change_password_page'
            ),
            'flexcore-notification-settings' => array(
                'title' => 'Notification',
                'content' => '[flexcore_notification_settings]',
                'option' => 'flexcore_notification_settings_page'
            ),
             'flexcore-avatar' => array(
                'title' => 'avatar',
                'content' => '[flexcore_avatar]',
                'option' => 'flexcore_avatar_page'
            ),
            'flexcore-my-account' => array(
                'title' => 'My Account',
                'content' => '[flexcore_my_account]',
                'option' => 'flexcore_my_account_page'
            ),
            'flexcore-register-myinfo' => array(
                'title' => 'Register with Singpass',
                'content' => '[flexcore_register_myinfo]',
                'option' => 'flexcore_register_myinfo_page'
            ),
            
        );

        foreach ($pages as $slug => $page) {
            $page_id = self::create_page($slug, $page['title'], $page['content']);
            if ($page_id) {
                update_option($page['option'], $page_id);
            }
        }

        // Set default options
        if (!get_option('flexcore_api_base_url')) {
            update_option('flexcore_api_base_url', 'http://localhost:3000');
        }

        if (!get_option('flexcore_token_storage')) {
            update_option('flexcore_token_storage', 'cookie');
        }

        if (!get_option('flexcore_enable_redirects')) {
            update_option('flexcore_enable_redirects', true);
        }

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Create a WordPress page if it doesn't exist
     *
     * @param string $slug
     * @param string $title
     * @param string $content
     * @return int|false The page ID on success, false on failure
     */
    private static function create_page($slug, $title, $content) {
        global $wpdb;

        if (WP_DEBUG) {
            error_log("FlexCore: Creating/Verifying page - {$slug}");
        }

        // Check if page exists by slug
        $page = get_page_by_path($slug);
        
        if ($page) {
            // Update existing page content if different
            if ($page->post_content !== $content) {
                wp_update_post(array(
                    'ID' => $page->ID,
                    'post_content' => $content
                ));
                if (WP_DEBUG) {
                    error_log("FlexCore: Updated existing page - {$slug}");
                }
            }
            return $page->ID;
        }

        // Create the page
        $page_id = wp_insert_post(array(
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_name' => $slug
        ));

        if ($page_id && !is_wp_error($page_id)) {
            if (WP_DEBUG) {
                error_log("FlexCore: Successfully created page - {$slug}");
            }
            return $page_id;
        } else {
            if (WP_DEBUG) {
                error_log("FlexCore Error: Failed to create page - {$slug}");
            }
            return false;
        }
    }
}
