<?php

/**
 * AJAX Handle
 *  Class
 *
 * @package FlexCore_Server
 */

// If this file is called directly, abort
if (!defined('WPINC')) {
    die;
}
// End of class FlexCore_Server_Ajax_Handler

// Include plugin dependencies
require_once(dirname(__FILE__) . '/class-flexcore-server-api.php');
require_once(dirname(__FILE__) . '/class-flexcore-server-utils.php');
require_once(dirname(__FILE__) . '/class-flexcore-server-session.php');

class FlexCore_Server_Ajax_Handler
{
    /**
     * Initialize AJAX hooks
     */
    public function __construct()
    {
        // Initialize session before anything else
        if (!session_id() && !headers_sent()) {
            session_start();
        }
        add_action('init', array($this, 'init'));
    }

    /**
     * Initialize WordPress environment and AJAX hooks
     */
    public function init()
    {
        if (!session_id()) {
            session_start();
        }

        // Add nonce verification for AJAX endpoints
        $this->register_ajax_endpoints();

        error_log("Handling get membership status request");
        // Check if user is authenticated
        if (FlexCore_Server_Session::is_authenticated()) {

            // Get the token from session
            $token = FlexCore_Server_Session::get_token();
            $api = new FlexCore_Server_API();

            // Call API to get membership status
            $response = $api->get_membership_status($token);
            error_log("API response for membership status: " . print_r($response, true));
            if (is_wp_error($response)) {
                wp_send_json_error(array('message' => $response['message'], 'errors' => $response['errors'] ?? []));
                return;
            }

            if (!empty($response['success']) && isset($response['membershipstatus'])) {
                FlexCore_Server_Session::set_user_membership_status($response['membershipstatus']);

                error_log("Status from session: " . print_r(FlexCore_Server_Session::get_user_membership_status(), true));
                // wp_send_json_success(array(
                //     'message' => $response['message'],
                //     'membershipstatus' => $response['membershipstatus']
                // ));
            }
        }
    }

    /**
     * Register AJAX endpoints with nonce verification
     */
    private function register_ajax_endpoints()
    {
        // Registration action
        // add_action('wp_ajax_flexcore_register', array($this, 'handle_register'));
        // add_action('wp_ajax_nopriv_flexcore_register', array($this, 'handle_register'));

        // Login related actions
        add_action('wp_ajax_flexcore_login', array($this, 'handle_login'));
        add_action('wp_ajax_nopriv_flexcore_login', array($this, 'handle_login'));
        add_action('wp_ajax_flexcore_verify_otp', array($this, 'handle_verify_otp'));
        add_action('wp_ajax_nopriv_flexcore_verify_otp', array($this, 'handle_verify_otp'));
        add_action('wp_ajax_flexcore_get_profile', array($this, 'handle_get_profile'));
        add_action('wp_ajax_nopriv_flexcore_get_profile', array($this, 'handle_get_profile'));
        add_action('wp_ajax_flexcore_update_profile', array($this, 'handle_update_profile'));
        add_action('wp_ajax_nopriv_flexcore_update_profile', array($this, 'handle_update_profile'));
        add_action('wp_ajax_flexcore_delete_account', array($this, 'handle_delete_account'));
        add_action('wp_ajax_nopriv_flexcore_delete_account', array($this, 'handle_delete_account'));
        add_action('wp_ajax_flexcore_logout', array($this, 'handle_logout'));
        add_action('wp_ajax_nopriv_flexcore_logout', array($this, 'handle_logout'));
        add_action('wp_ajax_nopriv_flexcore_forgot_password', array($this, 'handle_forgot_password'));
        add_action('wp_ajax_flexcore_forgot_password', array($this, 'handle_forgot_password'));
        add_action('wp_ajax_nopriv_flexcore_reset_password', array($this, 'handle_reset_password'));
        add_action('wp_ajax_flexcore_reset_password', array($this, 'handle_reset_password'));
        add_action('wp_ajax_flexcore_change_password', array($this, 'handle_change_password'));
        add_action('wp_ajax_nopriv_flexcore_change_password', array($this, 'handle_change_password'));
        add_action('wp_ajax_flexcore_notification_settings', array($this, 'handle_notification_settings'));
        add_action('wp_ajax_nopriv_flexcore_notification_settings', array($this, 'handle_notification_settings'));
        add_action('wp_ajax_flexcore_change_avatar', array($this, 'handle_avatar'));
        add_action('wp_ajax_nopriv_flexcore_change_avatar', array($this, 'handle_avatar'));
        add_action('wp_ajax_flexcore_register', array($this, 'handle_register'));
        add_action('wp_ajax_nopriv_flexcore_register', array($this, 'handle_register'));
        add_action('wp_ajax_flexcore_refer_friend', array($this, 'handle_refer_friend'));
        add_action('wp_ajax_nopriv_flexcore_refer_friend', array($this, 'handle_refer_friend'));
        add_action('wp_ajax_flexcore_get_rewards', array($this, 'handle_get_rewards'));
        add_action('wp_ajax_nopriv_flexcore_get_rewards', array($this, 'handle_get_rewards'));
        add_action('wp_ajax_flexcore_preview_rewards', array($this, 'handle_preview_rewards'));
        add_action('wp_ajax_nopriv_flexcore_preview_rewards', array($this, 'handle_preview_rewards'));

        add_action('wp_ajax_flexcore_get_survey', array($this, 'handle_survey'));
        add_action('wp_ajax_nopriv_flexcore_get_survey', array($this, 'handle_survey'));

        add_action('wp_ajax_flexcore_get_referral_history', array($this, 'handle_get_referral_history'));
        add_action('wp_ajax_nopriv_flexcore_get_referral_history', array($this, 'handle_get_referral_history'));

        add_action('wp_ajax_get_membership_status', array($this, 'handle_get_membership_status'));
        add_action('wp_ajax_nopriv_get_membership_status', array($this, 'handle_get_membership_status'));

        add_action('wp_ajax_flexcore_redeem_reward', array($this, 'handle_redeem_reward'));
        add_action('wp_ajax_nopriv_flexcore_redeem_reward', array($this, 'handle_redeem_reward'));

        add_action('wp_ajax_flexcore_verify_redeem_otp', array($this, 'handle_redeem_otp'));
        add_action('wp_ajax_nopriv_flexcore_verify_redeem_otp', array($this, 'handle_redeem_otp'));

        add_action('wp_ajax_flexcore_get_perks', array($this, 'handle_exclusive_perks'));
        add_action('wp_ajax_nopriv_flexcore_get_perks', array($this, 'handle_exclusive_perks'));


        add_action('wp_ajax_flexcore_get_account_details', array($this, 'handle_my_account_v2'));
        add_action('wp_ajax_nopriv_flexcore_get_account_details', array($this, 'handle_my_account_v2'));

        add_action('wp_ajax_flexcore_marged_register', array($this, 'handle_marged_register'));
        add_action('wp_ajax_nopriv_flexcore_marged_register', array($this, 'handle_marged_register'));

        add_action('wp_ajax_flexcore_postalcode_validation', array($this, 'handle_postalcode_validation'));
        add_action('wp_ajax_nopriv_flexcore_postalcode_validation', array($this, 'handle_postalcode_validation'));

    }

    /**
     * Handle registration request
     */
    // public function handle_register()
    // {
    //     // wp_die('This function was called.');
    //     wp_send_json_success(array(
    //         'message' => __('successs', 'flexcore-server'),
    //     ));
    //     check_ajax_referer('flexcore_register', 'nonce');


    //     $email = sanitize_email($_POST['email']);
    //     $name = sanitize_text_field($_POST['name']);
    //     $password = $_POST['password'];

    //     if (empty($email) || empty($name) || empty($password)) {
    //         wp_send_json_error(array('message' => __('All fields are required.', 'flexcore-server')));
    //     }

    //     if (!is_email($email)) {
    //         wp_send_json_error(array('message' => __('Invalid email address.', 'flexcore-server')));
    //     }

    //     // Check password strength
    //     $password_check = FlexCore_Server_Utils::check_password_strength($password);
    //     if ($password_check['strength'] < 3) {
    //         wp_send_json_error(array('message' => $password_check['message']));
    //     }

    //     $api = new FlexCore_Server_API();
    //     $response = $api->register($email, $name, $password);

    //     if (is_wp_error($response)) {
    //         wp_send_json_error(array('message' => $response->get_error_message()));
    //     }

    //     if ($response['success']) {
    //         wp_send_json_success(array(
    //             'message' => __('Registration successful. Please verify your email with OTP.', 'flexcore-server'),
    //             'redirect' => add_query_arg('email', urlencode($email), get_permalink(get_option('flexcore_verify_otp_page')))
    //         ));
    //     } else {
    //         wp_send_json_error(array(
    //             'message' => isset($response['message']) ?
    //                 $response['message'] :
    //                 __('Registration failed. Please try again.', 'flexcore-server')
    //         ));
    //     }
    // }

    /**
     * Handle login request
     */
    public function handle_login()
    {
        check_ajax_referer('flexcore_login', 'nonce');

        $email = sanitize_email($_REQUEST['email']);
        $password = $_REQUEST['password'];
        $login_attempts = isset($_REQUEST['login_attempts']) ? intval($_REQUEST['login_attempts']) : 0;

        if (empty($email) || empty($password)) {
            wp_send_json_error(array('message' => __('Email and password are required.', 'flexcore-server')));
        }

        $api = new FlexCore_Server_API();
        $response = $api->login($email, $password, $login_attempts);

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        }

        if ($response['success']) {
            wp_send_json_success(array(
                'message' => __('Please verify your email with OTP.', 'flexcore-server'),
                'redirect' => add_query_arg('email', urlencode($email), get_permalink(get_option('flexcore_verify_otp_page')))
            ));
        } else {
            wp_send_json_error(array('message' => $response['message']));
        }
    }

    /**
     * Handle OTP verification
     */
    public function handle_verify_otp()
    {
        check_ajax_referer('flexcore_verify_otp', 'nonce');

        $email = sanitize_email($_REQUEST['email']);
        $otp = sanitize_text_field($_REQUEST['otp']);

        if (empty($email) || empty($otp)) {
            wp_send_json_error(array('message' => $response['message'] ?? __(' OTP are required.', 'flexcore-server')));
        }

        $api = new FlexCore_Server_API();
        $response = $api->verify_login_otp($email, $otp);

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response['message']));
        }

        if (isset($response['success']) && $response['success']) {
            // Store authentication token and user profile in session
            if (isset($response['data']['token'])) {
                FlexCore_Server_Session::set_token($response['data']['token']);
            }

            if (isset($response['data'])) {
                FlexCore_Server_Session::set_user_profile($response['data']);
            }
            error_log("User profile after login: " . print_r($response['data'], true));
            error_log("User profile token after login: " . print_r($response['data']['token'], true));


            // Check if user has completed profile
            // $profile = FlexCore_Server_Session::get_user_profile();
            $profile = $response['data']['profile'] ?? null;
            $redirect_url = !empty($profile['name']) ?
                '/lifestyle-survey' : '/lifestyle-survey';

            wp_send_json_success(array(
                'message' => __('Login successful.', 'flexcore-server'),
                'redirect' => $redirect_url
            ));
        } else {
            wp_send_json_error(array(
                'message' => isset($response['message']) ?
                    $response['message'] :
                    __('OTP verification failed.', 'flexcore-server')
            ));
        }
    }

    /**
     * Handle get profile request
     */
    public function handle_get_profile()
    {
        check_ajax_referer('flexcore_get_profile', 'nonce');

        if (!FlexCore_Server_Session::is_authenticated()) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'flexcore-server')));
            return;
        }

        $token = FlexCore_Server_Session::get_token();
        $api = new FlexCore_Server_API();
        $response = $api->get_profile($token);

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
            return;
        }

        if ($response['success'] && isset($response['data'])) {
            // Store in session for future use
            FlexCore_Server_Session::set_user_profile($response['data']);
            wp_send_json_success($response['data']);
        } else {
            wp_send_json_error(array(
                'message' => isset($response['message']) ?
                    $response['message'] :
                    __('Failed to load profile data.', 'flexcore-server')
            ));
        }
    }

    /**
     * Handle update profile request
     */
    public function handle_update_profile()
    {
        check_ajax_referer('flexcore_update_profile', 'nonce');

        if (!FlexCore_Server_Session::is_authenticated()) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'flexcore-server')));
        }

        $token = FlexCore_Server_Session::get_token();
        $api = new FlexCore_Server_API();

        $data = array(
            // 'dateOfBirth' => sanitize_text_field($_POST['dateOfBirth']),
            // 'gender' => sanitize_text_field($_POST['gender']),
            // 'race' => sanitize_text_field($_POST['race']),
            // 'raceDetails' => sanitize_text_field($_POST['others']),
            'postalCode' => sanitize_text_field($_POST['postalCode']),
            'mobileNumber' => sanitize_text_field($_POST['mobileNumber']),
            'citizenship' => sanitize_text_field($_POST['citizenship'])
        );


        $response = $api->update_profile($token, $data);
        error_log("Response from API: " . print_r($response, true));
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response['errors']));
        }
        error_log("Response from API: " . print_r($response, true));
        if (!empty($response['success'])) {
            // Update session with new profile data
            // error_log("Updating user profile in session" . print_r($response['data'], true));
            // error_log("Updating user profile in session" . print_r($response['success'], true));
            if (isset($response['data'])) {
                FlexCore_Server_Session::set_user_profile($response['data']);
            }

            $redirect = isset($_POST['redirect_to_dashboard']) && $_POST['redirect_to_dashboard'] ?
                get_permalink(get_option('flexcore_dashboard_page')) : '';

            wp_send_json_success(array(
                'message' => $response['message'],
                'redirect' => $redirect
            ));
        } else {
            // Collect message and detailed errors
            $message = isset($response['errors']) ? $response['errors'] : __('Update failed.', 'flexcore-server');
            $details = '';

            if (!empty($response['errors']) && is_array($response['errors'])) {
                foreach ($response['errors'] as $field => $errors) {
                    foreach ((array) $errors as $errMsg) {
                        $details .= ucfirst($field) . ': ' . $errMsg . '<br>';
                    }
                }
            }

            wp_send_json_error(array(
                'message' => $message,
                'details' => $details
            ));
        }
    }

    /**
     * Handle delete account request
     */
    public function handle_delete_account()
    {
        check_ajax_referer('flexcore_delete_account', 'nonce');

        if (!FlexCore_Server_Session::is_authenticated()) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'flexcore-server')));
        }

        $token = FlexCore_Server_Session::get_token();
        $api = new FlexCore_Server_API();
        $response = $api->delete_account($token);

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        }

        if ($response['success']) {
            FlexCore_Server_Session::clear_token();
            FlexCore_Server_Session::clear_user_profile();
            wp_send_json_success(array(
                'message' => __('Your account has been deleted successfully.', 'flexcore-server'),
                'redirect' => get_permalink(get_option('
                
                '))
            ));
        } else {
            wp_send_json_error(array(
                'message' => isset($response['message']) ?
                    $response['message'] :
                    __('Failed to delete account.', 'flexcore-server')
            ));
        }
    }

    /**
     * Handle logout request
     */
    public function handle_logout()
    {
        check_ajax_referer('flexcore_logout', 'nonce');

        if (!FlexCore_Server_Session::is_authenticated()) {
            wp_send_json_error(array('message' => __('Already logged out.', 'flexcore-server')));
        }

        FlexCore_Server_Session::clear_all();

        wp_send_json_success(array(
            'message' => __('Successfully logged out.', 'flexcore-server'),
            'redirect' => get_permalink(get_option('flexcore_login_page'))
        ));
    }

    /**
     * Handle forgot password request
     */
    public function handle_forgot_password()
    {
        check_ajax_referer('flexcore_forgot_password', 'nonce');

        $email = sanitize_email($_POST['email']);

        if (empty($email)) {
            wp_send_json_error(array('message' => __('Email address is required.', 'flexcore-server')));
        }

        if (!is_email($email)) {
            wp_send_json_error(array('message' => __('Please enter a valid email address.', 'flexcore-server')));
        }

        $api = new FlexCore_Server_API();
        $response = $api->forgot_password($email);

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        }

        if ($response['success']) {
            wp_send_json_success(array(
                'message' =>  wp_send_json_success(array('message' => $response['message']))
            ));
        } else {
            wp_send_json_error(array(
                'message' => isset($response['message']) ?
                    $response['message'] :
                    __('Failed to send OTP.', 'flexcore-server')
            ));
        }
    }

    /**
     * Handle reset password request
     */
    public function handle_reset_password()
    {
        check_ajax_referer('flexcore_reset_password', 'nonce');

        $email = sanitize_email($_REQUEST['email']);
        $otp = sanitize_text_field($_REQUEST['otp']);
        $password = $_REQUEST['password'];

        if (empty($email) || empty($otp) || empty($password)) {
            wp_send_json_error(array('message' => __('All fields are required.', 'flexcore-server')));
            return;
        }

        if (!is_email($email)) {
            wp_send_json_error(array('message' => __('Invalid email address.', 'flexcore-server')));
            return;
        }

        // Validate OTP format
        if (!preg_match('/^\d{6}$/', $otp)) {
            wp_send_json_error(array('message' => __('Invalid OTP format.', 'flexcore-server')));
            return;
        }

        // Check password strength
        $password_check = FlexCore_Server_Utils::check_password_strength($password);
        if ($password_check['strength'] < 3) {
            wp_send_json_error(array('message' => $password_check['message']));
            return;
        }

        $api = new FlexCore_Server_API();
        $response = $api->reset_password_with_otp($email, $otp, $password);

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
            return;
        }

        if ($response['success']) {
            wp_send_json_success(array(
                'message' => __('Password has been reset successfully.', 'flexcore-server'),

            ));
        } else {
            wp_send_json_error(array(
                'message' => isset($response['message']) ?
                    $response['message'] :
                    __('Failed to reset password.', 'flexcore-server')
            ));
        }
    }

    /**
     * Handle change password request
     */
    public function handle_change_password()
    {
        check_ajax_referer('flexcore_change_password', 'nonce');

        if (!FlexCore_Server_Session::is_authenticated()) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'flexcore-server')));
            return;
        }

        $new_password = $_POST['new_password'];
        $old_password = $_POST['old_password'];

        // Validate password strength
        $password_check = FlexCore_Server_Utils::check_password_strength($new_password);
        if ($password_check['strength'] < 3) {
            wp_send_json_error(array('message' => $password_check['message']));
            return;
        }

        $token = FlexCore_Server_Session::get_token();
        $api = new FlexCore_Server_API();
        $response = $api->change_password($token, $new_password, $old_password);
        error_log("Response from API: " . print_r($response, true));
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response['message'] ?? __('An error occurred while changing password.', 'flexcore-server')));
            return;
        }

        if ($response['success']) {
            wp_send_json_success(
                array(
                    'message' => $response['message'] ?? __('Password changed successfully.', 'flexcore-server')
                )

            );
        } else {
            wp_send_json_error(array(
                'message' => isset($response['message']) ?
                    $response['message'] :
                    __('Failed to change password.', 'flexcore-server')
            ));
        }
    }
    public function handle_notification_settings()
    {
        // Verify nonce for security
        check_ajax_referer('flexcore_notification_settings', 'nonce');


        // Check if user is authenticated
        if (!FlexCore_Server_Session::is_authenticated()) {
            wp_send_json_error(array(
                'message' => __('Unauthorized access.', 'flexcore-server')
            ));
            return;
        }

        // Get user token
        $token = FlexCore_Server_Session::get_token();
        $api = new FlexCore_Server_API();

        // Sanitize and validate input data
        $email_notification = isset($_POST['emailNotifications']) ? sanitize_text_field($_POST['emailNotifications']) : false;
        $sms_notification = isset($_POST['smsNotifications']) ? sanitize_text_field($_POST['smsNotifications']) : false;

        // Prepare data for API
        $data = array(
            'smsNotification' => $sms_notification === 'true' ? true : false,
            'newsUpdateNotification' => $email_notification === 'true' ? true : false,
        );


        try {
            // Send request to API
            $response = $api->update_notification_settings($token, $data);
            error_log("Response from API: " . print_r($response, true));
            if (is_wp_error($response)) {
                wp_send_json_error(array(
                    'message' => $response->get_error_message()
                ));
                return;
            }

            if ($response['success']) {
                wp_send_json_success(array(
                    'message' => __('Notification settings updated successfully.', 'flexcore-server')
                ));
            } else {
                wp_send_json_error(array(
                    'message' => isset($response['message'])
                        ? $response['message']
                        : __('Failed to update notification settings.', 'flexcore-server')
                ));
            }
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => __('An error occurred while processing your request.', 'flexcore-server')
            ));
        }
    }
    public function handle_avatar()
    {
        error_log("Handling avatar change request");

        // Check nonce for security - use 'flexcore_nonce' as the name
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'flexcore_change_avatar')) {
            wp_send_json_error(array('message' => __('Nonce validation failed.', 'flexcore-server')));
            return;
        }



        // Check if the user is authenticated
        if (!FlexCore_Server_Session::is_authenticated()) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'flexcore-server')));
            return;
        }

        $token = FlexCore_Server_Session::get_token();
        $api = new FlexCore_Server_API();

        // Sanitize the avatarId from POST
        $avatarId = isset($_POST['avatarId']) ? sanitize_text_field($_POST['avatarId']) : '23400'; // Default avatar ID

        // Prepare data for the API request
        $data = array(
            'avatarId' => $avatarId,
        );

        // Send the update request to the API
        $response = $api->update_avatar_settings($token, $data);

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
            return;
        }

        if (!empty($response['success'])) {
            // Update session with new profile data
            // FlexCore_Server_Session::set_user_profile($response['data']);

            error_log("User profile after avatar change: " . print_r($response['data'], true));

            wp_send_json_success(array(
                'message' => __('Avatar updated successfully.', 'flexcore-server'),
                'avatarId' => $response['data']['metaData']['avatarId'] ?? $avatarId // Use the returned avatarId from the API response
            ));
        } else {
            wp_send_json_error(array(
                'message' => $response['message'] ?? __('Failed to update avatar.', 'flexcore-server')
            ));
        }
    }
    public function handle_refer_friend()
    {
        // error_log("Handling refer-a-friend request");

        // Step 1: Nonce check for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'flexcore_refer_nonce_action')) {
            wp_send_json_error(array('message' => __('Nonce validation failed.', 'flexcore-server')));
            return;
        }

        // Step 2: Check user authentication
        if (!FlexCore_Server_Session::is_authenticated()) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'flexcore-server')));
            return;
        }

        // Step 3: Sanitize referrer name
        $referral_name = isset($_POST['referral_name']) ? sanitize_text_field($_POST['referral_name']) : '';

        // Step 4: Sanitize and validate friend data (array of friends)
        $friend_data = isset($_POST['flexcore_friend_data']) && is_array($_POST['flexcore_friend_data'])
            ? $_POST['flexcore_friend_data']
            : [];

        $friends = [];
        foreach ($friend_data as $friend) {
            $name = isset($friend['name']) ? sanitize_text_field($friend['name']) : '';
            $email = isset($friend['email']) ? sanitize_email($friend['email']) : '';

            if ($name && is_email($email)) {
                $friends[] = [
                    'name'  => $name,
                    'email' => $email
                ];
            }
        }

        // Step 5: Validate required data
        if (empty($referral_name) || empty($friends)) {
            wp_send_json_error(array(
                'message' => __('Please enter your name and at least one valid friend.', 'flexcore-server')
            ));
            return;
        }

        // Step 6: Prepare token and call API
        $token = FlexCore_Server_Session::get_token();
        $api = new FlexCore_Server_API();

        $data = array(
            'referrer' => $referral_name,
            'friends'  => $friends
        );

        $response = $api->refer_friends($token, $data); // You need to implement this method in your API class

        // Step 7: Handle API Response
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
            return;
        }

        if (!empty($response['success'])) {
            wp_send_json_success(array(
                'message' => $response['data']['message'] ?? __('Thank you! Your friends have been referred.', 'flexcore-server')
            ));
        } else {
            wp_send_json_error(array(
                'message' => $response['message'] ?? __('Failed to process referral.', 'flexcore-server')
            ));
        }
    }
    public function handle_exclusive_perks()
    {
        // Check nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'flexcore-perks-nonce')) {
            wp_send_json_error(array('message' => __('Nonce validation failed.', 'flexcore-server')));
            return;
        }

        $api = new FlexCore_Server_API();

        // Call API to get rewards
        $response = $api->get_reward();

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
            return;
        }

        if (!empty($response['success'])) {
            $button = '';
            $buttons = [];

            $rewards = $response['data']['rewards'] ?? [];

            // Check if user is authenticated again (redundant but preserved from your logic)
            if (!FlexCore_Server_Session::is_authenticated()) {
                $login_page_id = get_option('flexcore_login_page'); // ID of the login page saved in options
                $login_page_url = get_permalink($login_page_id);
                $button = '<a class="hd-btn" href="' . esc_url($login_page_url) . '">LOGIN TO REDEEM</a>';

                foreach ($rewards as $reward) {
                    $link = home_url('/rewards_preview/?id=' . urlencode($reward['id']));
                    // $buttons[$reward['id']] = '<a class="hd-btn" href="' . esc_url($link) . '">SELECT</a>';
                    $buttons[$reward['id']] = $button;
                }
            } else {
                $token = FlexCore_Server_Session::get_token();
                $api = new FlexCore_Server_API();
                $profile_response = $api->get_profile($token);

                if (is_wp_error($profile_response)) {
                    wp_send_json_error(array('message' => $profile_response->get_error_message()));
                    return;
                }

                if ($profile_response['success'] && isset($profile_response['data'])) {
                    FlexCore_Server_Session::set_user_profile($profile_response['data']);
                    $profile_data = $profile_response['data'];
                } else {
                    wp_send_json_error(array(
                        'message' => isset($profile_response['message']) ?
                            $profile_response['message'] :
                            __('Failed to load profile data.', 'flexcore-server')
                    ));
                    return;
                }

                if (!empty($profile_data['metaData'])) {
                    if (isset($profile_data['metaData']['isProfileCompleted'])) {
                        if ($profile_data['metaData']['isProfileCompleted'] == true) {
                            foreach ($rewards as $reward) {
                                $link = home_url('/rewards_preview/?id=' . urlencode($reward['id']));
                                $buttons[$reward['id']] = '<a class="hd-btn" href="' . esc_url($link) . '">SELECT</a>';
                            }
                        } else {
                            $account_url = home_url('/my-account/');
                            $button = '<a class="hd-btn" href="' . esc_url($account_url) . '"> COMPLETE YOUR ACCOUNT TO REDEEM</a>';
                        }
                    }
                }
            }

            wp_send_json_success(array(
                'message' => __('Rewards retrieved successfully.', 'flexcore-server'),
                'data'    => array(
                    'rewards' => $rewards,
                    'button'  => $button,
                    'buttons' => $buttons,
                    'profile' => $profile_data ?? null
                )
            ));
        } else {
            wp_send_json_error(array(
                'message' => $response['message'] ?? __('Failed to retrieve rewards.', 'flexcore-server')
            ));
        }
    }
    public function handle_get_rewards()
    {
        error_log('Nonce sent: ' . ($_POST['nonce'] ?? 'none'));

        // Check nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'flexcore-reward-nonce')) {
            wp_send_json_error(array('message' => __('Nonce validation failed.', 'flexcore-server')));
            return;
        }

        $api = new FlexCore_Server_API();

        // Call API to get rewards
        $response = $api->get_reward();
        
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
            return;
        }

        if (!empty($response['success'])) {
            $button = '';
            $buttons = [];

            $rewards = $response['data']['rewards'] ?? [];

            // Check if user is authenticated again (redundant but preserved from your logic)
            if (!FlexCore_Server_Session::is_authenticated()) {
                $login_page_id = get_option('flexcore_login_page'); // ID of the login page saved in options
                $login_page_url = get_permalink($login_page_id);
                $button = '<a class="hd-btn" href="' . esc_url($login_page_url) . '">LOGIN TO REDEEM</a>';

                foreach ($rewards as $reward) {
                    $link = home_url('/rewards_preview/?id=' . urlencode($reward['id']));
                    // $buttons[$reward['id']] = '<a class="hd-btn" href="' . esc_url($link) . '">SELECT</a>';
                    $buttons[$reward['id']] = $button;
                }
            } else {
                $token = FlexCore_Server_Session::get_token();
                error_log("Token:".$token);
                $api = new FlexCore_Server_API();
                $profile_response = $api->get_profile($token);
                $response_current = $api->get_current_points($token);
                
                // error_log("response from points API: " . print_r($response_current, true));
                
                if (is_wp_error($profile_response)) {
                    wp_send_json_error(array('message' => $profile_response->get_error_message()));
                    return;
                }

                if ($profile_response['success'] && isset($profile_response['data'])) {
                    FlexCore_Server_Session::set_user_profile($profile_response['data']);
                    $profile_data = $profile_response['data'];
                    error_log("Profile Data: " . print_r($profile_data, true));
                } else {
                    wp_send_json_error(array(
                        'message' => isset($profile_response['message']) ?
                            $profile_response['message'] :
                            __('Failed to load profile data.', 'flexcore-server')
                    ));
                    return;
                }
                $membership_status = FlexCore_Server_Session::get_user_membership_status();
                if (!empty($profile_data['metaData'])) {
                    if (isset($profile_data['metaData']['isProfileCompleted'])) {
                        if ($profile_data['metaData']['isProfileCompleted'] == true && $membership_status == '4') {
                            foreach ($rewards as $reward) {
                                $link = home_url('/rewards_preview/?id=' . urlencode($reward['id']));
                                $buttons[$reward['id']] = '<a class="hd-btn" href="' . esc_url($link) . '">SELECT</a>';
                            }
                        } else {
                            $account_url = home_url('/my-account/');
                            $button = '<a class="hd-btn" href="' . esc_url($account_url) . '"> COMPLETE YOUR ACCOUNT TO REDEEM</a>';
                            foreach ($rewards as $reward) {
                                $buttons[$reward['id']] = $button;
                            }
                        }
                    }
                }
            }

            wp_send_json_success(array(
                'message' => __('Rewards retrieved successfully.', 'flexcore-server'),
                'data'    => array(
                    'rewards' => $rewards,
                    'button'  => $button,
                    'currentPoints' => $response_current['data']['currentPoints'] ?? 0,
                    'luckydrawChances' => $response_current['data']['totalLuckyDrawChance'] ?? 0,
                    'buttons' => $buttons,
                    'profile' => $profile_data ?? null // Include profile data if available
                )
            ));
        } else {
            wp_send_json_error(array(
                'message' => $response['message'] ?? __('Failed to retrieve rewards.', 'flexcore-server')
            ));
        }
    }


    public function handle_preview_rewards()
    {
        error_log('Preview reward ID: ' . $_POST['id']);

        error_log('Nonce sent 2: ' . ($_POST['nonce'] ?? 'none'));

        // Check nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'flexcore-reward-nonce')) {
            wp_send_json_error(array('message' => __('Nonce validation failed.', 'flexcore-server')));
            return;
        }

        // Validate reward ID
        if (empty($_POST['id'])) {
            wp_send_json_error(array('message' => __('Reward ID is missing.', 'flexcore-server')));
            return;
        }

        $reward_id = sanitize_text_field($_POST['id']);
        $token = FlexCore_Server_Session::get_token();
        $api = new FlexCore_Server_API();

        // Call API to get reward preview by ID
        $response = $api->get_reward_preview($reward_id);
        $response_current = $api->get_current_points($token);
        $response_user = $api->get_profile($token);

        error_log("Response User" . print_r($response_user, true));
        // $response = $api->get_referral_history($token, $data);
        error_log("API response for referral current: " . print_r($response_current, true));
        error_log("API response: " . print_r($response, true));
        if (is_wp_error($response)) {
            wp_send_json_error(array('id' => $reward_id, 'message' => $response->get_error_message()));
            return;
        }

        if (!empty($response['success']) && !empty($response['data'])) {

            $reward = $response['data']; // Since rewards is directly under data
            error_log("Reward data: " . print_r($reward, true));

            wp_send_json_success(array(
                'message' => __('Reward preview retrieved successfully.', 'flexcore-server'),
                'data'    => $reward,
                'currentPoints' => $response_current['data']['currentPoints'] ?? 0, // Assuming current points are in this structure
                'user' => $response_user['data']
            ));
        } else {
            wp_send_json_error(array(
                'message' => $response['message'] ?? __('Failed to retrieve reward preview.', 'flexcore-server')
            ));
        }
    }
    public function handle_redeem_otp()
    {
        error_log("Handling redeem OTP request");
        // Check nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'flexcore_otp_reward')) {
            wp_send_json_error(array('message' => __('Nonce validation failed.', 'flexcore-server')));
            return;
        }

        // Check if user is authenticated
        if (!FlexCore_Server_Session::is_authenticated()) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'flexcore-server')));
            return;
        }

        // Get the token from session
        $token = FlexCore_Server_Session::get_token();
        $api = new FlexCore_Server_API();

        // Get the reward ID and OTP from POST data
        $reward_id = isset($_POST['rewardId']) ? sanitize_text_field($_POST['rewardId']) : '';
        $otp = isset($_POST['otp']) ? sanitize_text_field($_POST['otp']) : '';
        $redeemRewardInitiateId = isset($_POST['redeemRewardInitiateId']) ? sanitize_text_field($_POST['redeemRewardInitiateId']) : '';

        // Validate inputs
        if (empty($reward_id) || empty($otp)) {
            wp_send_json_error(array('message' => __('Reward ID and OTP are required.', 'flexcore-server')));
            return;
        }

        // Call API to verify OTP and redeem reward
        $response = $api->verify_redeem_otp($token, $otp,  $reward_id, $redeemRewardInitiateId);
        error_log("Response from API: " . print_r($response, true));
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response['message'] ?? __('An error occurred while verifying OTP.', 'flexcore-server')));
            return;
        }

        if (!empty($response['success'])) {
            wp_send_json_success(array(
                'message' => $response['message'],
                'data'    => $response['data']
            ));
        } else {
            wp_send_json_error(array(
                'message' => isset($response['message']) ? $response['message'] : __('Failed to redeem reward.', 'flexcore-server')
            ));
        }
    }
    public function handle_survey()
    {
        // Check nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'flexcore_get_survey')) {
            wp_send_json_error(array('message' => __('Nonce validation failed.', 'flexcore-server')));
            return;
        }
        // Check if user is authenticated
        if (!FlexCore_Server_Session::is_authenticated()) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'flexcore-server')));
            return;
        }
        // Get the token from session
        $token = FlexCore_Server_Session::get_token();
        // Create API instance
        $api = new FlexCore_Server_API();
        error_log(
            "Handling survey request with token: " . (is_null($token) ? 'null' : $token)
        );
        $response = $api->get_survey($token);
        // Check for API errors     
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
            return;
        }

        // Check if the response is successful
        if (!empty($response['success']) && !empty($response['data'])) {
            // Store survey data in session

            $surveyUrl = $response['data']['redirectUrl'] ?? '';
            // Return success response with survey data
            wp_send_json_success(array(
                'message' => __('Survey retrieved successfully.', 'flexcore-server'),
                'data'    => $surveyUrl
            ));
        } else {
            wp_send_json_error(array(
                'message' => isset($response['message']) ? $response['message'] : __('Failed to retrieve survey.', 'flexcore-server')
            ));
        }


        // Get the survey ID from POST data
    }


   
    public function handle_get_referral_history()
    {
        
        error_log("Handling referral history request");
        // Check nonce for security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'flexcore_get_referral_history')) {
            wp_send_json_error(array('message' => __('Nonce validation failed.', 'flexcore-server')));
            return;
        }

        // Check if user is authenticated
        if (!FlexCore_Server_Session::is_authenticated()) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'flexcore-server')));
            return;
        }

        // Get the token from session
        $token = FlexCore_Server_Session::get_token();
        $api = new FlexCore_Server_API();
        error_log("Handling referral history request with token: " . (is_null($token) ? 'null' : $token));
        // Call API to get referral history
        $from = $_POST['from'];
        $to = $_POST['to'];
        $data = array(
            'from' => $from,
            'to' => $to
        );



        $response = $api->get_referral_history($token, $from, $to);
        
        error_log(('API response for referral history: ' . print_r($response, true)));
        $pointResponse = $api->get_point_history($token, $from, $to);
        error_log(('API response for point history: ' . print_r($pointResponse, true)));
        
        $response_current = $api->get_current_points($token);
        // Prepare response parts
        $referralSuccess = !is_wp_error($response) && !empty($response['success']) && !empty($response['result']['referrals']);
        $pointSuccess = !is_wp_error($pointResponse) && !empty($pointResponse['success']) && !empty($pointResponse['data']);

        $referralData = $referralSuccess ? $response['result']['referrals'] : [];
        $pointData = $pointSuccess ? $pointResponse['data'] : [];

        $referralMessage = $referralSuccess ? '' : ($response['message'] ?? 'No referral history found.');
        $pointMessage = $pointSuccess ? '' : ($pointResponse['message'] ?? 'No points history found.');

        wp_send_json_success([
            'data' => [
                'history' => $referralData,
              
                
                 'currentPoints' => $response_current['data']['currentPoints'] ?? 0,
                    'luckyDrawChances' => $response_current['data']['totalLuckyDrawChance'] ?? 0,
                'referralMessage' => $referralMessage,
                'pointHistory' => $pointData,
                'pointMessage' => $pointMessage,
            ],
        ]);
    }





    public function handle_register()
    {


        // Check nonce presence
        if (!isset($_POST['register_nonce'])) {
            error_log("Registration error: nonce missing in POST data.");
            wp_send_json_error(array(
                'message' => __('Security check failed: nonce is missing.', 'flexcore-server'),
                'error_code' => 'nonce_missing'
            ));
            return;
        }

        // Verify nonce
        if (!wp_verify_nonce($_POST['register_nonce'], 'flexcore_register')) {
            error_log("Registration error: nonce verification failed.");
            wp_send_json_error(array(
                'message' => __('Security check failed: nonce verification failed.', 'flexcore-server'),
                'error_code' => 'nonce_invalid'
            ));
            return;
        }

        // Sanitize inputs
        $email          = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $name           = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $password       = isset($_POST['password']) ? $_POST['password'] : '';
        $referral_code  = isset($_POST['referral_code']) ? sanitize_text_field($_POST['referral_code']) : '';
        $utm_string     = isset($_POST['utm_string']) ? sanitize_text_field($_POST['utm_string']) : '';
        $campaign_id    = isset($_POST['campaign_id']) ? sanitize_text_field($_POST['campaign_id']) : '';
        $register_source = isset($_POST['register_source']) ? sanitize_text_field($_POST['register_source']) : '';

        // Validate required fields
        if (empty($email) || empty($name) || empty($password)) {
            error_log("Registration error: Missing required fields. Email: '{$email}', Name: '{$name}', Password set: " . ($password ? 'Yes' : 'No'));
            wp_send_json_error(array(
                'message' => __('Please fill all required fields.', 'flexcore-server'),
                'error_code' => 'fields_missing'
            ));
            return;
        }

        // Validate email format
        if (!is_email($email)) {
            error_log("Registration error: Invalid email format '{$email}'.");
            wp_send_json_error(array(
                'message' => __('Invalid email address.', 'flexcore-server'),
                'error_code' => 'invalid_email'
            ));
            return;
        }

        // Prepare data for API call
        $data = array(
            'email'           => $email,
            'name'            => $name,
            'password'        => $password,
            'referral_code'   => $referral_code,
            'utm_string'      => $utm_string,
            'campaign_id'     => $campaign_id,
            'register_source' => $register_source,
        );

        // Call API
        $api = new FlexCore_Server_API();
        $response = $api->register_user($data);
        error_log("Response from API: " . print_r($response, true));
        // Handle API errors
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            error_log("Registration error: API returned WP_Error - {$error_message}");
            wp_send_json_error(array(
                'message' => $error_message,
                'error_code' => 'api_wp_error'
            ));
            return;
        }
        error_log("API response for registration: " . print_r($response, true));
        if (!empty($response['success'])) {
            error_log("Registration success for email '{$email}'.");
             if (isset($response['data']['token'])) {
                FlexCore_Server_Session::set_token($response['data']['token']);
            }

            if (isset($response['data'])) {
                FlexCore_Server_Session::set_user_profile($response['data']);
            }
            error_log("User profile after login: " . print_r($response['data'], true));
            error_log("User profile token after login: " . print_r($response['data']['token'], true));
            error_log("Token in session after login: " . print_r(FlexCore_Server_Session::get_token(), true));
            
            wp_send_json_success(array(
                'message' => $response['data']['message'] ?? __('Registration successful.', 'flexcore-server'),
                
            ));
        } else {
            $error_msg = $response['message'] ?? __('Registration failed.', 'flexcore-server');
            error_log("Registration failed: API response failure - {$error_msg}");
            wp_send_json_error(array(
                'message' => $error_msg,
                'error_code' => 'api_failure'
            ));
        }
    }
    public function handle_redeem_reward()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'flexcore_redeem_voucher')) {
            wp_send_json_error(array('message' => __('Nonce validation failed.', 'flexcore-server')));
            return;
        }

        if (!FlexCore_Server_Session::is_authenticated()) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'flexcore-server')));
            return;
        }

        $token = FlexCore_Server_Session::get_token();
        $api = new FlexCore_Server_API();

        // Decode data JSON string
        $data = isset($_POST['data']) ? json_decode(stripslashes($_POST['data']), true) : [];

        // Access values safely
        $reward_id = isset($data['rewardId']) ? sanitize_text_field($data['rewardId']) : '';
        $quantity = isset($data['quantity']) ? intval($data['quantity']) : 1;
        $name = isset($data['name']) ? sanitize_text_field($data['name']) : '';
        $email = isset($data['email']) ? sanitize_email($data['email']) : '';
        $mobile = isset($data['mobile']) ? sanitize_text_field($data['mobile']) : '';
        $address = isset($data['address']) ? sanitize_text_field($data['address']) : '';
        if (empty($reward_id)) {
            wp_send_json_error(array('message' => __('Reward ID is required.', 'flexcore-server')));
            return;
        }

        // You can now pass all values to your redeem API
        $response = $api->redeem_reward($token, $reward_id, $quantity, $name, $email, $mobile, $address);
        error_log("API response for redeeming reward: " . print_r($response, true));
        //         'mobile' => $mobile,
        //         {
        //   "quantity": 2,
        //   "name": "John Doe",
        //   "email": "john.doe@example.com",
        //   "mobileNumber": "+6591234567",
        //   "address": "123 Orchard Road, Singapore"
        // }


        error_log("API response for redeeming reward: " . print_r($response, true));

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' =>  $response['message']));
            return;
        }

        if (!empty($response['success'])) {
            wp_send_json_success(array(
                'message' => $response['message'],
                'id' => $response['redeemRewardInitiateId']
            ));
        } else {
            wp_send_json_error(array(
                'message' => $response['message'],
            ));
        }
    }

    public function handle_get_membership_status()
    {
        // Check nonce for security
        error_log("Handling get membership status request");
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'flexcore_get_membership_status')) {
            wp_send_json_error(array('message' => __('Nonce validation failed.', 'flexcore-server')));
            return;
        }

        // Check if user is authenticated
        if (!FlexCore_Server_Session::is_authenticated()) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'flexcore-server')));
            return;
        }

        // Get the token from session
        $token = FlexCore_Server_Session::get_token();
        $api = new FlexCore_Server_API();

        // Call API to get membership status
        $response = $api->get_membership_status($token);
        error_log("API response for membership status: " . print_r($response, true));
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response['message'], 'errors' => $response['errors'] ?? []));
            return;
        }

        if (!empty($response['success']) && isset($response['membershipstatus'])) {
            wp_send_json_success(array(
                'message' => $response['message'],
                'membershipstatus' => $response['membershipstatus']
            ));
        } else {
            wp_send_json_error(array(
                'message' => isset($response['message']) ? $response['message'] : __('Failed to retrieve membership status.', 'flexcore-server')
            ));
        }
    }
    public function handle_my_account_v2()
    {
        error_log("Handling get membership status request");
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'flexcore_get_my_account_v2')) {
            wp_send_json_error(array('message' => __('Nonce validation failed.', 'flexcore-server')));
            return;
        }

        // Check if user is authenticated
        if (!FlexCore_Server_Session::is_authenticated()) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'flexcore-server')));
            return;
        }

        // Get the token from session
        $token = FlexCore_Server_Session::get_token();
        $api = new FlexCore_Server_API();
        // Call API to get membership status
        $response = $api->get_profile($token);
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response['message'], 'errors' => $response['errors'] ?? []));
            return;
        }
        if (!empty($response['success']) && isset($response['data'])) {
            wp_send_json_success(array(
                'message' => $response['message'],
                'data' => $response['data']
            ));
        } else {
            wp_send_json_error(array(
                'message' => isset($response['message']) ? $response['message'] : __('Failed to retrieve Account Details.', 'flexcore-server')
            ));
        }
    }

    public function handle_marged_register()
    {
        // Check nonce presence
        if (!isset($_POST['register_nonce'])) {
            error_log("Registration error: nonce missing in POST data.");
            wp_send_json_error(array(
                'message' => __('Security check failed: nonce is missing.', 'flexcore-server'),
                'error_code' => 'nonce_missing'
            ));
            return;
        }

        // Verify nonce
        if (!wp_verify_nonce($_POST['register_nonce'], 'flexcore_register')) {
            error_log("Registration error: nonce verification failed.");
            wp_send_json_error(array(
                'message' => __('Security check failed: nonce verification failed.', 'flexcore-server'),
                'error_code' => 'nonce_invalid'
            ));
            return;
        }

        // Sanitize inputs
        $fullName       = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $email          = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $password       = isset($_POST['password']) ? $_POST['password'] : '';
        $referralCode   = isset($_POST['referral_code']) ? sanitize_text_field($_POST['referral_code']) : '';
        $utmString      = isset($_POST['utm_string']) ? sanitize_text_field($_POST['utm_string']) : '';
        $campaignId     = isset($_POST['campaign_id']) ? sanitize_text_field($_POST['campaign_id']) : '';
        $source         = isset($_POST['register_source']) ? sanitize_text_field($_POST['register_source']) : '';
        $gender         = isset($_POST['gender']) ? sanitize_text_field($_POST['gender']) : '';
        $race           = isset($_POST['race']) ? sanitize_text_field($_POST['race']) : '';
        

        $dateOfBirth    = isset($_POST['dob']) ? sanitize_text_field($_POST['dob']) : '';
        $mobileNumber   = isset($_POST['mobile']) ? sanitize_text_field($_POST['mobile']) : '';
        $citizenship    = isset($_POST['citizenship']) ? sanitize_text_field($_POST['citizenship']) : '';
        $postalCode     = isset($_POST['postal_code']) ? sanitize_text_field($_POST['postal_code']) : '';
        $raceDetails    = isset($_POST['others']) ? sanitize_text_field($_POST['others']) : '';
        $preferredName  = isset($_POST['preferredName']) ? sanitize_text_field($_POST['preferredName']) : '';
        $maritalStatus = isset($_POST['maritalStatus']) ? sanitize_text_field($_POST['maritalStatus']) : '';
        $singPass      = isset($_POST['singPass']) ? (int) $_POST['singPass'] : 0;

        // Only Singapore Citizens and Permanent Residents can register
        $allowedCitizenship = ['singaporecitizen', 'permanentResident'];
        if (!in_array($citizenship, $allowedCitizenship, true)) {
            wp_send_json_error(array(
                'message' => __('Only Singapore Citizens and Permanent Residents are eligible to register.', 'flexcore-server'),
                'error_code' => 'citizenship_not_allowed'
            ));
            return;
        }

        // Validate required fields
        if (empty($email) || empty($fullName) || empty($password)) {
            wp_send_json_error(array(
                'message' => __('Please fill all required fields.', 'flexcore-server'),
                'error_code' => 'fields_missing'
            ));
            return;
        }

        if (!is_email($email)) {
            wp_send_json_error(array(
                'message' => __('Invalid email address.', 'flexcore-server'),
                'error_code' => 'invalid_email'
            ));
            return;
        }

        // Prepare request body for external API
        $data = array(
            "fullName"      => $fullName,
            "email"         => $email,
            "password"      => $password,
            "referralCode"  => $referralCode,
            "utmCode"     => $utmString,
            "campaignId"    => $campaignId,
            "source"        => $source,
            "gender"        => $gender,
            "race"          => $race,
            "raceDetails"   => $raceDetails,
            "dateOfBirth"   => $dateOfBirth,
            "mobileNumber"  => $mobileNumber,
            "citizenship"   => $citizenship,
            "postalCode"    => $postalCode,
            "preferredName" => $preferredName,
            "maritalStatus" => $maritalStatus,
            "singPass" => $singPass
        );
        $api = new FlexCore_Server_API();
        $response = $api->merged_register($data);
        error_log("Merged registration response: " . print_r($response, true));
        if (is_wp_error($response)) {
            wp_send_json_error(array(
                'message' => $response['message'] ?? __('An error occurred.', 'flexcore-server'),
                'error_code' => 'api_wp_error'
            ));
            return;
        }

        if (!empty($response['success'])) {
             if (isset($response['data']['token'])) {
                FlexCore_Server_Session::set_token($response['data']['token']);
            }

            if (isset($response['data'])) {
                FlexCore_Server_Session::set_user_profile($response['data']);
            }
            error_log("User profile after login: " . print_r($response['data'], true));
            error_log("User profile token after login: " . print_r($response['data']['token'], true));
            error_log("Token in session after login: " . print_r(FlexCore_Server_Session::get_token(), true));
            
            
            error_log("Registration success for email '{$email}'.");
            wp_send_json_success(array(
                'message' => $response['data']['message'] ?? __('Registration successful.', 'flexcore-server'),
                'response' => $response,
                'redirect' => add_query_arg('email', urlencode($email), get_permalink(get_option('flexcore_login_page')))
            ));
        } else {
            $error_msg = $response['message'] ?? __('Registration failed.', 'flexcore-server');
            error_log("Registration failed: API response failure - {$error_msg}");
            $errors = isset($response['errors']) ? $response['errors'] : [];
            wp_send_json_error(array(
                'message' => $error_msg,
                'response' => $response,
                'errors' => $errors
            ));
        }
    }
function refresh_authToken() {
    $api = new FlexCore_Server_API();
    $get_token = $api->refresh_authToken();
    if ($get_token && isset($get_token["access_token"], $get_token["expiry_timestamp"])) {
        error_log("Refreshed token: " . $get_token["access_token"]);
        return $get_token;
    } else {
        error_log("Failed to refresh OneMap token.");
        return false;
    }

}
    function get_valid_onemap_token() {
    global $wpdb;
    $table = $wpdb->prefix . 'onemap_token'; // adjust if your table prefix is different

    // Fetch token row
    $token_data = $wpdb->get_row("SELECT * FROM $table WHERE id = 1", ARRAY_A);

    $current_timestamp = time();
    $three_days_in_seconds = 3 * 24 * 60 * 60; // 259200 seconds

    // If no token in DB, or expiry is missing, refresh
    if (!$token_data || empty($token_data['access_token']) || empty($token_data['expiry_timestamp'])) {
        error_log("No token found. Refreshing...");
        $new_token_data = $this->refresh_authToken();
        if ($new_token_data && isset($new_token_data['access_token'], $new_token_data['expiry_timestamp'])) {
            $this->save_onemap_token($new_token_data['access_token'], $new_token_data['expiry_timestamp']);
            return $new_token_data['access_token'];
        }
        return false;
    }

    $access_token = $token_data['access_token'];
    $expiry_timestamp = $token_data['expiry_timestamp'];

    // Check if token expired
    if ($current_timestamp >= $expiry_timestamp) {
        error_log("Token expired. Refreshing...");
        $new_token_data = $this->refresh_authToken();
        if ($new_token_data && isset($new_token_data['access_token'], $new_token_data['expiry_timestamp'])) {
            $this->save_onemap_token($new_token_data['access_token'], $new_token_data['expiry_timestamp']);
            return $new_token_data['access_token'];
        } else {
            error_log("Failed to refresh token.");
            return false;
        }
    }

    // Token still valid
    return $access_token;
}

    function save_onemap_token($access_token, $expiry_timestamp) {
    global $wpdb;

    $table = $wpdb->prefix . "onemap_token"; // or just 'onemap_token' if no prefix

    // Check if row exists
    $exists = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE id = 1");

    if ($exists) {
        // Update the existing row
        $wpdb->update(
            $table,
            [
                'access_token'     => $access_token,
                'expiry_timestamp' => $expiry_timestamp,
                'updated_at'       => current_time('mysql')
            ],
            ['id' => 1]
        );
    } else {
        // Insert a new row
        $wpdb->insert(
            $table,
            [
                'id'               => 1,
                'access_token'     => $access_token,
                'expiry_timestamp' => $expiry_timestamp,
                'updated_at'       => current_time('mysql')
            ]
        );
    }
    //check error occursd during database operation
    if ($wpdb->last_error) {
        error_log("Database error while saving OneMap token: " . $wpdb->last_error);
        return false;
    } else {
        error_log("OneMap token saved successfully.");
        return true;
    }

}

   public function handle_postalcode_validation() {
    // Check nonce presence
    if (!isset($_POST['register_nonce'])) {
        error_log("Postal code Validation error: nonce missing in POST data.");
        wp_send_json_error([
            'message' => __('Security check failed: nonce is missing.', 'flexcore-server'),
            'error_code' => 'nonce_missing'
        ]);
        return;
    }

    // Verify nonce
    if (!wp_verify_nonce($_POST['register_nonce'], 'flexcore_register')) {
        error_log("Postal Code Validation error: nonce verification failed.");
        wp_send_json_error([
            'message' => __('Security check failed: nonce verification failed.', 'flexcore-server'),
            'error_code' => 'nonce_invalid'
        ]);
        return;
    }

    // Sanitize inputs
    $postalCode = isset($_POST['postal_code']) ? sanitize_text_field($_POST['postal_code']) : '';

    // Validate required fields
    if (empty($postalCode)) {
        error_log("Postal code validation error: Missing postal code.");
        wp_send_json_error([
            'message' => __('Please provide a postal code.', 'flexcore-server'),
            'error_code' => 'fields_missing'
        ]);
        return;
    }

    $api = new FlexCore_Server_API();

    // First attempt to get a valid token
    $get_token = $this->get_valid_onemap_token();
    error_log("Initial token response: " . print_r($get_token, true));

    if (empty($get_token)) {
        wp_send_json_error([
            'message' => __('Failed to obtain valid OneMap token.', 'flexcore-server'),
            'error_code' => 'token_error'
        ]);
        return;
    }

    // Attempt postal code validation
    $response = $api->validate_postal_code($postalCode, $get_token);
    error_log("Postal code validation response: " . print_r($response, true));

    // Check if token was invalid
    if (isset($response['error']) && stripos($response['error'], 'Invalid authentication token') !== false) {
        error_log("Token invalid, refreshing token and retrying...");

        // Force token refresh
        $get_token = $this->refresh_authToken();
        if (!empty($get_token['access_token']) && !empty($get_token['expiry_timestamp'])) {
            // Save new token to DB
            $this->save_onemap_token($get_token['access_token'], $get_token['expiry_timestamp']);
            $get_token = $get_token['access_token'];

            // Retry the API call once
            $response = $api->validate_postal_code($postalCode, $get_token);
            error_log("Postal code validation response after refresh: " . print_r($response, true));
        } else {
            wp_send_json_error([
                'message' => __('Failed to refresh OneMap token.', 'flexcore-server'),
                'error_code' => 'token_refresh_failed'
            ]);
            return;
        }
    }

    // Return final response
    wp_send_json_success([
        'message' => $response['message'] ?? __('Postal code validation completed.', 'flexcore-server'),
        'response' => $response
    ]);
}

}