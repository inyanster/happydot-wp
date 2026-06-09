<?php

/**
 * The public-facing functionality of the plugin
 *
 * @package FlexCore_Server
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}



// Include plugin dependencies
require_once(plugin_dir_path(dirname(__FILE__)) . 'includes/class-flexcore-server-template-loader.php');
require_once(plugin_dir_path(dirname(__FILE__)) . 'includes/class-flexcore-server-session.php');

class FlexCore_Server_Public
{
    /**
     * Initialize the class
     */
    public function __construct()
    {
        error_log('FlexCore: Public class constructor called');
        $this->register_shortcodes();

        // Only add these actions if we haven't already
        if (!has_action('wp_enqueue_scripts', array($this, 'enqueue_styles'))) {
            add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        }
    }

    /**
     * Initialize the public functionality
     */
    public function init()
    {
        if (WP_DEBUG) {
            error_log('FlexCore: Init action called');
        }
        add_filter('the_content', array($this, 'process_shortcodes'), 20);

        // Fallback: theme uses get_the_content() which bypasses the_content filter.
        // Buffer the page output after the theme renders, then replace raw
        // [flexcore_...] shortcode tags with their rendered HTML.
        add_action('the_post', array($this, 'register_shortcode_fallback'), 5);
    }

    /**
     * When the theme uses get_the_content() without do_shortcode,
     * our shortcodes never render. This output buffer catches the
     * shortcode text and replaces it with rendered HTML at page render time.
     */
        /**
     * Theme uses get_the_content() which bypasses the_content filters.
     * After the theme renders, we capture the full page output at shutdown
     * and replace any remaining raw [flexcore_...] shortcode tags with
     * their rendered HTML so they appear correctly in the browser.
     */
    public function register_shortcode_fallback()
    {
        global $post;
        error_log('FlexCore the_post: fired for post ' . @$post->ID);
        if (!$post || !isset($post->ID)) {
            error_log('FlexCore the_post: no post');
            return;
        }
        if (!has_shortcode($post->post_content, 'flexcore_register_myinfo')) {
            error_log('FlexCore the_post: no flexcore_register_myinfo');
            return;
        }
        error_log('FlexCore the_post: pre-rendering for post ' . $post->ID);
        $post->post_content = do_shortcode($post->post_content);
        error_log('FlexCore the_post: done, post_content len=' . strlen($post->post_content));
    }

    /**
     * Intercept shortcode rendering BEFORE WordPress's do_shortcode runs.
     * This is the correct place to handle flexcore shortcodes because:
     * - It fires per-shortcode, before any theme/Elementor output buffers
     * - The returned HTML replaces the shortcode tag directly
     * - No risk of double-processing or nested OB conflicts
     */
    public function override_shortcode_rendering($override, $tag, $attr)
    {
        // Only handle our flexcore shortcodes
        $render_methods = array(
            'flexcore_register_myinfo'          => 'render_register_myinfo_form',
            'flexcore_lifestyle_survey_button' => 'render_lifestyle_survey_button_form',
        );

        if (!isset($render_methods[$tag])) return $override;

        $method = $render_methods[$tag];
        if (!method_exists($this, $method)) {
            error_log("FlexCore: pre_do_shortcode_tag - method {$method} not found");
            return $override;
        }

        error_log("FlexCore: pre_do_shortcode_tag - {$tag} calling method={$method}");
        try {
            $result = @$this->$method($attr);
        } catch (Throwable $e) {
                return $override;
        }

        if (empty($result) || is_wp_error($result)) {
                return $override;
        }

        error_log("FlexCore: pre_do_shortcode_tag - {$tag} -> " . strlen($result) . " chars");
        return $result;
    }

    /**
     * Process shortcodes in content
     */
    public function process_shortcodes($content)
    {
        if (is_singular() && is_main_query()) {
            if (WP_DEBUG) {
                $original_content = $content;
                error_log('FlexCore: Processing content with shortcodes');
                $processed_content = do_shortcode($content);

                if ($original_content === $processed_content && preg_match('/\[flexcore_[^\]]+\]/', $content)) {
                    error_log('FlexCore: Shortcode was not processed: ' . $content);
                    // Check if shortcode is registered
                    global $shortcode_tags;
                    if (WP_DEBUG) {
                        error_log('FlexCore: Registered shortcodes: ' . print_r(array_keys($shortcode_tags), true));
                    }
                }
                return $processed_content;
            }
            return do_shortcode($content);
        }
        return $content;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(
            'flexcore-server-public',
            plugin_dir_url(__FILE__) . 'css/flexcore-server-public.css',
            array(),
            FLEXCORE_SERVER_VERSION,
            'all'
        );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     */
    public function enqueue_scripts()
    {
        global $post;

        // Main public script
        wp_enqueue_script(
            'flexcore-server-public',
            plugin_dir_url(__FILE__) . 'js/flexcore-server-public.js',
            array('jquery'),
            FLEXCORE_SERVER_VERSION,
            false
        );
        wp_enqueue_script(
            'flexcore-membersip-status',
            plugin_dir_url(__FILE__) . 'js/modules/getMembershipStatus.js',
            array('jquery'),
            FLEXCORE_SERVER_VERSION,
            false
        );

        // Localize the script with new data
        wp_localize_script('flexcore-server-public', 'flexcoreServerAjax', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('flexcore-server-nonce'),
            'rewardNonce' => wp_create_nonce('flexcore-reward-nonce'),
            'perksNonce' => wp_create_nonce('flexcore-perks-nonce'),
            'logoutNonce' => wp_create_nonce('flexcore_logout'),
            'profileNonce' => wp_create_nonce('flexcore_get_profile'),
            'surveyNonce' => wp_create_nonce('flexcore_get_survey'),
            'referralHistoryNonce' => wp_create_nonce('flexcore_get_referral_history'),
            'membershipStatusNonce' => wp_create_nonce('flexcore_get_membership_status'),
            'myAccountV2Nonce' => wp_create_nonce('flexcore_get_my_account_v2'),
            'updateProfileNonce' => wp_create_nonce('flexcore_update_profile'),
            'changePasswordNonce' => wp_create_nonce('flexcore_change_password'),
            'redeemNonce'=>wp_create_nonce('flexcore_redeem_voucher'),
            'otpRewardNonce' => wp_create_nonce('flexcore_otp_reward'),
            'loginUrl' => get_permalink(get_option('flexcore_login_page')),
            'registerUrl' => get_permalink(get_option('flexcore_register_page')),
            'dashboardUrl' => get_permalink(get_option('flexcore_dashboard_page')),
            'resetPasswordUrl' => get_permalink(get_option('flexcore_reset_password_page')),
            'profileUrl' => get_permalink(get_option('flexcore_profile_page')),
            'myAccountUrl' => get_permalink(get_option('flexcore_my_account_page')),
            'i18n' => array(
                'loading' => __('Loading...', 'flexcore-server'),
                'loginFailed' => __('Login failed. Please try again.', 'flexcore-server'),
                'errorOccurred' => __('An error occurred.', 'flexcore-server'),
                'verificationFailed' => __('OTP verification failed.', 'flexcore-server'),
                'tryAgain' => __('Please try again.', 'flexcore-server'),
                'pleaseConfirmDelete' => __('Please confirm that you want to delete your account.', 'flexcore-server'),
                'confirmDeleteAccount' => __('Are you absolutely sure you want to delete your account? This action cannot be undone.', 'flexcore-server'),
                'deleteFailed' => __('Failed to delete account. Please try again.', 'flexcore-server'),
                'returnToLogin' => __('Return to login', 'flexcore-server'),
                'logoutConfirm' => __('Are you sure you want to logout?', 'flexcore-server'),
                'invalidDateFormat' => __('Please enter a valid date in YYYY-MM-DD format.', 'flexcore-server'),
                'invalidMobileNumber' => __('Please enter a valid international phone number starting with country code.', 'flexcore-server'),
                'loadFailed' => __('Failed to load profile data. Please try again.', 'flexcore-server'),
                'updateFailed' => __('Failed to update profile. Please try again.', 'flexcore-server'),
                'fillRequired' => __('Please fill in all required fields.', 'flexcore-server'),
                'consentRequired' => __('Please confirm that you agree to the data usage consent.', 'flexcore-server'),
                'passwordsDoNotMatch' => __('The passwords you entered do not match.', 'flexcore-server'),
                'passwordChanged' => __('Password changed successfully.', 'flexcore-server'),
                'passwordChangeFailed' => __('Failed to change password.', 'flexcore-server')
            )
        ));

        // Only load specific module scripts if we're on the relevant page
        if ($post instanceof WP_Post) {
            if (has_shortcode($post->post_content, 'flexcore_login')) {
                wp_enqueue_script(
                    'flexcore-server-login',
                    plugin_dir_url(__FILE__) . 'js/modules/login.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }
            if (has_shortcode($post->post_content, 'flexcore_lifestyle_survey_button')) {
                wp_enqueue_script(
                    'flexcore-server-lifestyle-survey',
                    plugin_dir_url(__FILE__) . 'js/modules/survey.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }
            if (has_shortcode($post->post_content, 'flexcore_Complete_ProfileOrSurvey')) {
                wp_enqueue_script(
                    'flexcore_Complete_ProfileOrSurvey',
                    plugin_dir_url(__FILE__) . 'js/modules/completeAccount.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }
            if (has_shortcode($post->post_content, 'flexcore_register')) {
                // wp_enqueue_script(
                //     'flexcore-server-register',
                //     plugin_dir_url(__FILE__) . 'js/modules/registration.js',
                //     array('jquery', 'flexcore-server-public'),
                //     FLEXCORE_SERVER_VERSION,
                //     true
                // );
                // wp_enqueue_script(
                //     'flexcore-server-register-js', // unique handle for register.js
                //     plugin_dir_url(__FILE__) . 'js/modules/register.js',
                //     array('jquery', 'flexcore-server-public'),
                //     FLEXCORE_SERVER_VERSION,
                //     true
                // );
                wp_enqueue_script(
                    'flexcore-server-merged-register',
                    plugin_dir_url(__FILE__) . 'js/modules/mergedRegistration.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }

            if (has_shortcode($post->post_content, 'flexcore_referral_signup')) {
                wp_enqueue_script(
                    'flexcore-server-referral-register',
                    plugin_dir_url(__FILE__) . 'js/modules/registration.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
                wp_enqueue_script(
                    'flexcore-server-referral-register-js', // unique handle for register.js
                    plugin_dir_url(__FILE__) . 'js/modules/referral-register.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }
            if (has_shortcode($post->post_content, 'flexcore_referral_history')) {
                wp_enqueue_script(
                    'flexcore-server-referral-history',
                    plugin_dir_url(__FILE__) . 'js/modules/referral-history.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }
            if (has_shortcode($post->post_content, 'flexcore_my_account_v2')) {
                wp_enqueue_script(
                    'flexcore-my-account-v2',
                    plugin_dir_url(__FILE__) . 'js/modules/myAccountV2.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
                wp_enqueue_script(
                    'flexcore-my-account-take-survey',
                    plugin_dir_url(__FILE__) . 'js/modules/survey.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }
            if (has_shortcode($post->post_content, 'flexcore_verify_otp')) {
                wp_enqueue_script(
                    'flexcore-server-verify-otp',
                    plugin_dir_url(__FILE__) . 'js/modules/verify-otp.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }
            if (has_shortcode($post->post_content, 'flexcore_referfriend')) {
                wp_enqueue_script(
                    'flexcore-server-verify-refer-friend',
                    plugin_dir_url(__FILE__) . 'js/modules/referFriend.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }
            if (has_shortcode($post->post_content, 'flexcore_rewards')) {
                wp_enqueue_script(
                    'flexcore-server-verify-otp',
                    plugin_dir_url(__FILE__) . 'js/modules/rewards.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }
             if (has_shortcode($post->post_content, 'flexcore_exclusive_perks')) {
                wp_enqueue_script(
                    'flexcore-server-exclusive-perks',
                    plugin_dir_url(__FILE__) . 'js/modules/exclusivePerk.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }

            if (has_shortcode($post->post_content, 'flexcore_dashboard')) {
                wp_enqueue_script(
                    'flexcore-server-logout',
                    plugin_dir_url(__FILE__) . 'js/modules/dashboard.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }

            if (has_shortcode($post->post_content, 'flexcore_forgot_password')) {
                wp_enqueue_script(
                    'flexcore-server-forgot-password',
                    plugin_dir_url(__FILE__) . 'js/modules/forgot-password.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }

            if (has_shortcode($post->post_content, 'flexcore_reset_password')) {
                wp_enqueue_script(
                    'flexcore-server-reset-password',
                    plugin_dir_url(__FILE__) . 'js/modules/reset-password.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }

            if (has_shortcode($post->post_content, 'flexcore_profile')) {
                wp_enqueue_script(
                    'flexcore-server-profile',
                    plugin_dir_url(__FILE__) . 'js/modules/profile.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }

            if (has_shortcode($post->post_content, 'flexcore_delete_account')) {
                wp_enqueue_script(
                    'flexcore-server-delete-account',
                    plugin_dir_url(__FILE__) . 'js/modules/delete-account.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }

            if (has_shortcode($post->post_content, 'flexcore_change_password')) {
                wp_enqueue_script(
                    'flexcore-server-change-password',
                    plugin_dir_url(__FILE__) . 'js/modules/change-password.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }
            if (has_shortcode($post->post_content, 'flexcore_notification_settings')) {
                wp_enqueue_script(
                    'flexcore-server-change-notification-settings',
                    plugin_dir_url(__FILE__) . 'js/modules/change-notification-settings.js',

                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }
            if (FlexCore_Server_Session::is_authenticated()) {
                wp_enqueue_script(
                    'flexcore-server-logout',
                    plugin_dir_url(__FILE__) . 'js/modules/logout.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }
            if (has_shortcode($post->post_content, 'flexcore_rewards_preview')) {
                wp_enqueue_script(
                    'flexcore-server-rewards-preview',
                    plugin_dir_url(__FILE__) . 'js/modules/rewards-preview.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
                wp_enqueue_script(
                    'flexcore-server-rewards-preview-form', // unique handle for register.js
                    plugin_dir_url(__FILE__) . 'js/modules/rewards-preview-form.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
            }
            if (has_shortcode($post->post_content, 'flexcore_avatar')) {

                // Enqueue JS
                wp_enqueue_script(
                    'flexcore-server-avatar',
                    plugin_dir_url(__FILE__) . 'js/modules/avatar.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );
                wp_enqueue_script(
                    'flexcore-server-survey-refer-friend',
                    plugin_dir_url(__FILE__) . 'js/modules/survey.js',
                    array('jquery', 'flexcore-server-public'),
                    FLEXCORE_SERVER_VERSION,
                    true
                );

                // Get profile data
                // $profile_data = FlexCore_Server_Session::get_user_profile();
                $token = FlexCore_Server_Session::get_token();
                $api = new FlexCore_Server_API();
                $profile_data = $api->get_profile($token);
                // wp_send_json_error($profile_data);
                // Localize it for JS
                wp_localize_script('flexcore-server-avatar', 'FlexcoreProfile', array(
                    'profileData' => $profile_data
                ));
            }
        }
    }

    /**
     * Register all shortcodes
     */
    private function register_shortcodes()
    {
        error_log('FlexCore: Registering shortcodes');
        $shortcodes = array(
            'flexcore_login' => 'render_login_form',
            'flexcore_verify_otp' => 'render_verify_otp_form',
            'flexcore_dashboard' => 'render_dashboard',
            'flexcore_profile' => 'render_profile_form',
            'flexcore_forgot_password' => 'render_forgot_password_form',
            'flexcore_reset_password' => 'render_reset_password_form',
            'flexcore_change_password_button' => 'render_change_password_button',

            'flexcore_delete_account' => 'render_delete_account_form',
            'flexcore_change_password' => 'render_change_password_form',
            'flexcore_signup_button' => 'render_signup_button',
            'flexcore_register' => 'render_sregister_form',
            "flexcore_notification_settings" => "render_notification_settings_form",
            "flexcore_avatar" => "render_avatar",
            "flexcore_Complete_ProfileOrSurvey" => "render_Complete_ProfileOrSurvey",
            "flexcore_referfriend" => "render_referfreind_form",
            "flexcore_rewards" => "render_rewards",
            "flexcore_exclusive_perks" => "render_exclusive_perks",
            "flexcore_rewards_preview" => "render_rewards_preview",

            "flexcore_referral_history" => "render_referral_history",
            "flexcore_referral_signup" => 'render_referral_signup_form',
            
            "flexcore_my_account_v2" => 'render_my_account_v2_form',
            "flexcore_lifestyle_survey_button" => 'render_lifestyle_survey_button_form',
            "flexcore_register_myinfo" => 'render_register_myinfo_form'


        );

        foreach ($shortcodes as $tag => $callback) {
            error_log("FlexCore: Registering shortcode [{$tag}]");
            add_shortcode($tag, array($this, $callback));
        }

        // Add debug filter to log shortcode processing
        if (WP_DEBUG) {
            add_filter('pre_do_shortcode_tag', array($this, 'override_shortcode_rendering'), 10, 3);
        }
    }

    /**
     * Handle unauthorized access
     *
     * @return string
     */
    private function handle_unauthorized()
    {
        $login_page = get_option('flexcore_login_page');
        if ($login_page && get_option('flexcore_enable_redirects', true)) {
            wp_redirect(get_permalink($login_page));
            exit;
        }
        return FlexCore_Server_Template_Loader::load_template('unauthorized', array(
            'login_url' => get_permalink($login_page)
        ));
    }

    /**
     * Render login form
     */
    public function render_login_form($atts)
    {
        ob_start();
        if (FlexCore_Server_Session::is_authenticated()) {
            $dashboard_page = get_option('flexcore_dashboard_page');
            if ($dashboard_page && get_option('flexcore_enable_redirects', true)) {
                wp_redirect(get_permalink($dashboard_page));
                exit;
            }
            $content = FlexCore_Server_Template_Loader::load_template('already-logged-in', array(
                'dashboard_url' => get_permalink($dashboard_page)
            ));
        } else {
            $content = FlexCore_Server_Template_Loader::load_template('login-form');
        }
        echo $content;
        return ob_get_clean();
    }

    /**
     * Render verify OTP form
     */
    public function render_verify_otp_form($atts)
    {
        ob_start();
        if (FlexCore_Server_Session::is_authenticated()) {
            $dashboard_page = get_option('flexcore_dashboard_page');
            if ($dashboard_page && get_option('flexcore_enable_redirects', true)) {
                wp_redirect(get_permalink($dashboard_page));
                exit;
            }
            $content = FlexCore_Server_Template_Loader::load_template('already-logged-in', array(
                'dashboard_url' => get_permalink($dashboard_page)
            ));
        } else {
            $email = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';
            if (empty($email)) {
                $content = FlexCore_Server_Template_Loader::load_template('error', array(
                    'message' => __('Invalid or missing email address. Please try logging in again.', 'flexcore-server')
                ));
            } else {
                $content = FlexCore_Server_Template_Loader::load_template('verify-otp-form', array(
                    'email' => $email
                ));
            }
        }
        echo $content;
        return ob_get_clean();
    }

    /**
     * Render dashboard
     */
    public function render_dashboard($atts)
    {
        ob_start();
        if (!FlexCore_Server_Session::is_authenticated()) {
            echo $this->handle_unauthorized();
            return ob_get_clean();
        }

        $profile = array(
            'success' => true,
            'data' => FlexCore_Server_Session::get_user_profile()
        );

        if (!$profile['data']) {
            $content = FlexCore_Server_Template_Loader::load_template('error', array(
                'message' => __('Error loading profile data.', 'flexcore-server')
            ));
        } else {
            $content = FlexCore_Server_Template_Loader::load_template('dashboard', array(
                'profile' => $profile
            ));
        }
        echo $content;
        return ob_get_clean();
    }

    /**
     * Render forgot password form
     */
    public function render_forgot_password_form($atts)
    {
        if (FlexCore_Server_Session::is_authenticated()) {
            $dashboard_page = get_option('flexcore_dashboard_page');

            //'resetPasswordUrl' => get_permalink(get_option('flexcore_reset_password_page')),
            if ($dashboard_page && get_option('flexcore_enable_redirects', true)) {
                wp_redirect(get_permalink($dashboard_page));
                exit;
            }
            return FlexCore_Server_Template_Loader::load_template('already-logged-in', array(
                'dashboard_url' => get_permalink($dashboard_page)
            ));
        }

        return FlexCore_Server_Template_Loader::load_template('forgot-password-form');
    }

    /**
     * Render reset password form
     */
    public function render_reset_password_form($atts)
    {
        if (FlexCore_Server_Session::is_authenticated()) {
            $dashboard_page = get_option('flexcore_dashboard_page');
            if ($dashboard_page && get_option('flexcore_enable_redirects', true)) {
                wp_redirect(get_permalink($dashboard_page));
                exit;
            }
            return FlexCore_Server_Template_Loader::load_template('already-logged-in', array(
                'dashboard_url' => get_permalink($dashboard_page)
            ));
        }

        $email = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';
        if (empty($email)) {
            return FlexCore_Server_Template_Loader::load_template('error', array(
                'message' => __('Invalid or missing email address. Please try the password reset process again.', 'flexcore-server')
            ));
        }

        return FlexCore_Server_Template_Loader::load_template('reset-password-form', array(
            'email' => $email
        ));
    }

    /**
     * Render profile form
     */
    public function render_profile_form($atts)
    {
        if (!FlexCore_Server_Session::is_authenticated()) {
            return $this->handle_unauthorized();
        }

        return FlexCore_Server_Template_Loader::load_template('profile-form');
    }

    /**
     * Render delete account form
     */
    public function render_delete_account_form($atts)
    {
        if (!FlexCore_Server_Session::is_authenticated()) {
            return $this->handle_unauthorized();
        }

        return FlexCore_Server_Template_Loader::load_template('delete-account-form');
    }

    /**
     * Render change password form
     */
    public function render_change_password_form($atts)
    {
        if (!FlexCore_Server_Session::is_authenticated()) {
            return $this->handle_unauthorized();
        }

        return FlexCore_Server_Template_Loader::load_template('change-password-form');
    }
    public function render_signup_button($atts)
    {
        if (!FlexCore_Server_Session::is_authenticated()) {
            return FlexCore_Server_Template_Loader::load_template('signup-button-guest');
        }

        return FlexCore_Server_Template_Loader::load_template('signup-button-authenticated');
    }
    public function render_notification_settings_form($atts)
    {
        if (!FlexCore_Server_Session::is_authenticated()) {
            return $this->handle_unauthorized();
        }
        $token = FlexCore_Server_Session::get_token();
        $api = new FlexCore_Server_API();
        $response = $api->get_profile($token);

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
            return;
        }

        if ($response['success'] && isset($response['data'])) {
            FlexCore_Server_Session::set_user_profile($response['data']);
            $profile_data = $response['data'];
        } else {
            wp_send_json_error(array(
                'message' => isset($response['message']) ?
                    $response['message'] :
                    __('Failed to load profile data.', 'flexcore-server')
            ));
        }

        $sms = false;
        $email = false;

        if (!empty($profile_data['metaData'])) {
            if (isset($profile_data['metaData']['smsNotification'])) {
                $sms = $profile_data['metaData']['smsNotification'] && $profile_data['metaData']['smsNotification'] === true ? true : false;
            }
            if (isset($profile_data['metaData']['newsUpdateNotification'])) {
                $email = $profile_data['metaData']['newsUpdateNotification'] && $profile_data['metaData']['newsUpdateNotification'] === true ? true : false;
            }
        }

        return FlexCore_Server_Template_Loader::load_template('notification-settings', [
            'sms' => $sms,
            'email' => $email,
        ]);
    }
    public function render_referral_history($atts)
    {
        if (!FlexCore_Server_Session::is_authenticated()) {
            return $this->handle_unauthorized();
        }
        return FlexCore_Server_Template_Loader::load_template('referral-history');
    }
    /**
     * Render notification settings form 
     */

    public function render_avatar($atts)
    {
        if (!FlexCore_Server_Session::is_authenticated()) {
            return $this->handle_unauthorized();
        }

        return FlexCore_Server_Template_Loader::load_template('avatar');
    }
    /**
     * Render Complete Profile or Survey form
     */
public function render_Complete_ProfileOrSurvey($atts)
{
    if (!FlexCore_Server_Session::is_authenticated()) {
        return $this->handle_unauthorized();
    }

    $token = FlexCore_Server_Session::get_token();
    $api = new FlexCore_Server_API();

    try {
        $response = $api->get_profile($token);
    } catch (Exception $e) {
        error_log("FlexCore: Backend API error - " . $e->getMessage());
        $response = FlexCore_Server_Session::get_user_profile();

        if (empty($response) || !is_array($response)) {
            return __('Unable to load your profile at the moment. Please try again later.', 'flexcore-server');
        }
    }

    // Safeguard: validate response structure
    if (!is_array($response) || !isset($response['success']) || !isset($response['data'])) {
        error_log("FlexCore: Invalid API response format.");
        return __('An error occurred while loading your profile.', 'flexcore-server');
    }

    if (!$response['success']) {
        return __('Failed to load profile data.', 'flexcore-server');
    }

    if (!empty($response['data'])) {
        FlexCore_Server_Session::set_user_profile($response['data']);
    }
   
    $profile_data = FlexCore_Server_Session::get_user_profile();
    error_log("FlexCore: Profile data loaded - " . print_r($profile_data, true));
    if (empty($profile_data)) {
        return __('Your profile data could not be loaded.', 'flexcore-server');
    }

    $meta = $response['data']['metaData'] ?? [];
    error_log("FlexCore: Meta data loaded - " . print_r($meta, true));
    error_log("FlexCore: Lifestyle status - " . (isset($response['data']['lifestyleStatus']) ? $response['data']['lifestyleStatus'] : 'not set'));
    if ((empty($meta['isProfileCompleted']))) {
        return FlexCore_Server_Template_Loader::load_template('isAccountCompleted');
    } elseif (((int)$response['data']['lifestyleStatus']) === 2) {
        return FlexCore_Server_Template_Loader::load_template('isSurveyCompleted');
    } elseif (((int)$response['data']['lifestyleStatus']) === 1) {
        return FlexCore_Server_Template_Loader::load_template('profileCompleted');
    }

    return null;
}

    public function render_referfreind_form($atts)
    {
        if (!FlexCore_Server_Session::is_authenticated()) {
            return $this->handle_unauthorized();
        }

        // return FlexCore_Server_Template_Loader::load_template('refer-a-friend');
        return FlexCore_Server_Template_Loader::load_template('social-sharing');
    }

    public function render_rewards($atts)
    {
        // if (!FlexCore_Server_Session::is_authenticated()) {
        //     return $this->handle_unauthorized();
        // }     


        return FlexCore_Server_Template_Loader::load_template('rewards');
    }
    public function render_exclusive_perks($atts)
    {
        if (!FlexCore_Server_Session::is_authenticated()) {
            return $this->handle_unauthorized();
        }

        return FlexCore_Server_Template_Loader::load_template('exclusivePerks​');
    }
    public function render_rewards_preview($atts)
    {
        if (!FlexCore_Server_Session::is_authenticated()) {
            return $this->handle_unauthorized();
        }

        return FlexCore_Server_Template_Loader::load_template('rewards-preview');
    }
    public function render_change_password_button($atts)
    {
        if (!FlexCore_Server_Session::is_authenticated()) {
            return $this->handle_unauthorized();
        }


        return FlexCore_Server_Template_Loader::load_template('change-password-button');
    }
    /**
     * Render registration form
     */
    public function render_sregister_form($atts)
    {
        ob_start();
        if (FlexCore_Server_Session::is_authenticated()) {
            $dashboard_page = get_option('flexcore_dashboard_page');
            if ($dashboard_page && get_option('flexcore_enable_redirects', true)) {
                wp_redirect(get_permalink($dashboard_page));
                exit;
            }
            $content = FlexCore_Server_Template_Loader::load_template('already-logged-in', array(
                'dashboard_url' => get_permalink($dashboard_page)
            ));
        } else {
            // $content = FlexCore_Server_Template_Loader::load_template('register-form');
            $content = FlexCore_Server_Template_Loader::load_template('margedRegistration');
        }
        echo $content;
        return ob_get_clean();
    }

    public function render_referral_signup_form($atts)
    {
        ob_start();
        if (FlexCore_Server_Session::is_authenticated()) {
            $dashboard_page = get_option('flexcore_dashboard_page');
            if ($dashboard_page && get_option('flexcore_enable_redirects', true)) {
                wp_redirect(get_permalink($dashboard_page));
                exit;
            }
            $content = FlexCore_Server_Template_Loader::load_template('already-logged-in', array(
                'dashboard_url' => get_permalink($dashboard_page)
            ));
        } else {
            $content = FlexCore_Server_Template_Loader::load_template('referral-register-form');
        }
        echo $content;
        return ob_get_clean();
    }
    
    public function render_my_account_v2_form($atts)
    {
        if (!FlexCore_Server_Session::is_authenticated()) {
            return $this->handle_unauthorized();
        }

        return FlexCore_Server_Template_Loader::load_template('myAccountV2');
        // return FlexCore_Server_Template_Loader::load_template('lifestyle-survey');
    }
    public function render_lifestyle_survey_button_form($atts)
    {
        if (!FlexCore_Server_Session::is_authenticated()) {
            return $this->handle_unauthorized();
        }

        return FlexCore_Server_Template_Loader::load_template('lifestyle-survey');
    }

    public function render_register_myinfo_form($atts)
    {
        $is_auth = FlexCore_Server_Session::is_authenticated();
        error_log("FlexCore render_register_myinfo_form: START is_auth={$is_auth}");
        if ($is_auth) {
            $dashboard_page = get_option('flexcore_dashboard_page');
            if ($dashboard_page && get_option('flexcore_enable_redirects', true)) {
                error_log("FlexCore render_register_myinfo_form: auth=true, doing wp_redirect");
                wp_redirect(get_permalink($dashboard_page));
                exit;
            }
            $content = FlexCore_Server_Template_Loader::load_template('already-logged-in', array(
                'dashboard_url' => get_permalink($dashboard_page)
            ));
        } else {
            $content = FlexCore_Server_Template_Loader::load_template('register-myinfo');
        }
        return $content;
    }
}
