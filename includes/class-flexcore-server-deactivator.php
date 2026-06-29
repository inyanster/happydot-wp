<?php

/**
 * Fired during plugin deactivation
 */
class FlexCore_Server_Deactivator {

    /**
     * Clean up plugin data and options
     */
    public static function deactivate() {
        // Only clean up if requested (can be made configurable via admin settings)
        if (get_option('flexcore_cleanup_on_deactivate', false)) {
            // Remove plugin options
            $options = array(
                'flexcore_api_base_url',
                'flexcore_token_storage',
                'flexcore_login_page',
                'flexcore_verify_otp_page',
                'flexcore_dashboard_page',
                'flexcore_profile_page',
                'flexcore_forgot_password_page',
                'flexcore_reset_password_page',
                'flexcore_delete_account_page',
                'flexcore_enable_redirects',
                'flexcore_cleanup_on_deactivate'
            );

            foreach ($options as $option) {
                delete_option($option);
            }

            // Remove created pages (optional, can be made configurable)
            $pages = array(
                'flexcore-login',
                'flexcore-verify-otp',
                'flexcore-dashboard',
                'flexcore-profile',
                'flexcore-forgot-password',
                'flexcore-reset-password',
                'flexcore-delete-account'
            );

            foreach ($pages as $page_slug) {
                $page = get_page_by_path($page_slug);
                if ($page) {
                    wp_delete_post($page->ID, true);
                }
            }
        }

        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
