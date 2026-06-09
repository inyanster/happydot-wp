<?php
/**
 * API Client Class
 *
 * @package FlexCore_Server
 */

class FlexCore_Server_API {
    /**
     * API base URL
     *
     * @var string
     */
    private $api_url;

    /**
     * Initialize the API client
     */
    public function __construct() {
        $this->api_url = get_option('flexcore_api_base_url', '');
        
    }

    /**
     * Make an API request
     *
     * @param string $endpoint API endpoint
     * @param array $args Request arguments
     * @return array|WP_Error Response data or WP_Error
     */
    private function request($endpoint, $args = array()) {
        if (empty($this->api_url)) {
            return new WP_Error('api_url_missing', __('API URL is not configured.', 'flexcore-server'));
        }

        $url = trailingslashit($this->api_url) . ltrim($endpoint, '/');
        $defaults = array(
            'method' => 'POST',
            'timeout' => 30,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking' => true,
            'headers' => array(
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ),
            'cookies' => array()
        );

        $args = wp_parse_args($args, $defaults);

        // Convert body to JSON if it's an array
        if (isset($args['body']) && is_array($args['body'])) {
            $args['body'] = wp_json_encode($args['body']);
        }
        
        $response = wp_remote_request($url, $args);
        error_log('API Request: ' . print_r($response, true));
        if (is_wp_error($response)) {
            error_log('API Request Error: ' . $response->get_error_message());            FlexCore_Server_Utils::debug_log('API Error: ' . $response->get_error_message());
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            FlexCore_Server_Utils::debug_log('JSON Decode Error: ' . json_last_error_msg());
            return new WP_Error('json_decode_error', __('Something Went wrong, Please try again later...', 'flexcore-server'));
        }

        return $data;
    }

    /**
     * Login request
     *
     * @param string $email User email
     * @param string $password User password
     * @param int $login_attempts Number of login attempts
     * @return array|WP_Error Response data
     */
    public function login($email, $password, $login_attempts = 0) {
        return $this->request('api/v1/auth/wp-login', array(
            'method' => 'POST',
            'body' => array(
                'email' => $email,
                'password' => $password,
            )
        ));
    }
  public function get_referral_history($token, $from, $to) {
    $response = $this->request('api/v1/referrals/history', array(
        'method' => 'POST',
        'headers' => array(
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json' 
        ),
        'body' => json_encode(array(
            'from' => $from,
            'to' => $to
        ))
    ));

    error_log('Referral History Response: ' . print_r($response, true) . $token . ' ' . $from . ' ' . $to);
    return $response;
}
    // Prepare the URL-encoded body
  public function redeem_reward(
    string $token,
    string $reward_id,
    int    $quantity,
    string $name  = '',
    string $email = '',
    string $mobile = '',
    string $address = ''
) {
    // Build the form‑encoded payload.
    // `array_filter` removes keys whose value is '' or null.
    $body = http_build_query(
        array_filter([
            'quantity'     => $quantity,           // always present
            'name'         => $name,
            'email'        => $email,
            'mobileNumber' => $mobile,
            'address'      => $address,
        ], static fn($v) => $v !== '' && $v !== null)
    );

    // Compose endpoint: …/redeem/{id}/initiate
    $url = 'api/v1/rewards/redeem/' . $reward_id . '/initiate';

    // Fire the request
    return $this->request($url, [
        'method'  => 'POST',
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type'  => 'application/x-www-form-urlencoded',
            'Accept'        => 'application/json',
        ],
        'body' => $body,
    ]);
}

public function verify_redeem_otp($token, $otp, $redeem_id, $redeemRewardInitiateId) {
    error_log('Verify Redeem OTP: ' . print_r(array(
        'token' => $token,
        'otp' => $otp,
        'redeem_id' => $redeem_id
    ), true));
    $response = $this->request('api/v1/rewards/redeem/verify', array(
        'method' => 'POST',
        'headers' => array(
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ),
        'body' => json_encode(array(
            'code' => $otp,
            'redeemRewardInitiateId' => $redeemRewardInitiateId
        ))
    ));
    
    error_log('Verify Redeem OTP Response: ' . print_r($response, true));
    return $response;
}

    // Check for specific error conditions
    

public function get_membership_status($token) {
    $response = $this->request('api/v1/user/membership-status', array(
        'method' => 'POST',
        'headers' => array(
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ),
    ));
    error_log('Membership Status Response: ' . print_r($response, true));
    return $response;
}
 public function get_point_history($token, $from, $to) {
    $response = $this->request("api/v1/user/points-history?from={$from}&to={$to}", array(
        'method' => 'GET',
        'headers' => array(
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'text/plain' 
        ),
       
    ));

    error_log('Point History Response: ' . print_r($response, true) );
    return $response;
}
public function get_current_points($token) {
    $response = $this->request('api/v1/user/points', array(
        'method' => 'GET',
        'headers' => array(
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json' 
        ),
       
    ));

    
    return $response;
}
                
    /**
     * Verify OTP for login
     *
     * @param string $email User email
     * @param string $otp OTP code
     * @return array|WP_Error Response data
     */
    public function verify_login_otp($email, $otp) {
        return $this->request('api/v1/auth/otp/verify-login', array(
            'method' => 'POST',
            'body' => array(
                'email' => $email,
                'code' => $otp
            )
        ));
    }

    /**
     * Get user profile data
     *
     * @param string $token Authentication token
     * @return array|WP_Error Response data
     */
    public function get_profile($token) {
        if (empty($token)) {
            return new WP_Error('missing_token', __('Authentication token is required.', 'flexcore-server'));
        }

        $response = $this->request('api/v1/user/profile', array(
            'method' => 'GET',
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            )
        ));

        // Debug logging
        if (WP_DEBUG) {
            error_log('FlexCore API: Profile response: ' . print_r($response, true));
        }

        if (is_wp_error($response)) {
            error_log('FlexCore API Error: ' . $response->get_error_message());
            return $response;
        }

        // Check for specific error conditions
        if (isset($response['code']) && $response['code'] === 401) {
            return new WP_Error('unauthorized', __('Your session has expired. Please log in again.', 'flexcore-server'));
        }

        return $response;
    }

    /**
     * Update profile
     *
     * @param string $token Authentication token
     * @param array $data Profile data
     * @return array|WP_Error Response data
     */
    public function update_profile($token, $data) {
        $response= $this->request('api/v1/user/profile', array(
            'method' => 'PATCH',
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ),
            'body' => $data
        ));
        // Debug logging
        error_log('FlexCore API: Update profile response: ' . print_r($response, true));
        return $response;
    }
    public function get_survey($token) {
        return $this->request('api/v1/surveys/account-lifestyle', array(
            'method' => 'GET',
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ),
        ));
    }
    /**
     * Forgot password request
     *
     * @param string $email User email
     * @return array|WP_Error Response data
     */
    public function forgot_password($email) {
        return $this->request('api/v1/auth/forgot-password', array(
            'method' => 'POST',
            'body' => array(
                'email' => $email
            )
        ));
    }

    /**
     * Reset password
     *
     * @param string $token Reset token
     * @param string $password New password
     * @return array|WP_Error Response data
     */
    public function reset_password($token, $password) {
        return $this->request('auth/reset-password', array(
            'method' => 'POST',
            'body' => array(
                'token' => $token,
                'password' => $password
            )
        ));
    }

    /**
     * Reset password with OTP
     * 
     * @param string $email User's email
     * @param string $otp OTP code
     * @param string $password New password
     * @return array|WP_Error Response data
     */
    public function reset_password_with_otp($email, $otp, $password) {
        return $this->request('api/v1/auth/reset-password', array(
            'method' => 'POST',
            'body' => array(
                'email' => $email,
                'code' => $otp,
                'password' => $password
            )
        ));
    }

    /**
     * Delete account
     *
     * @param string $token Authentication token
     * @return array|WP_Error Response data
     */
    public function delete_account($token) {
        return $this->request('api/v1/user/profile', array(
            'method' => 'DELETE',
            'headers' => array(
                'Authorization' => 'Bearer ' . $token
            )
        ));
    }

    /**
     * Refresh token
     *
     * @param string $token Current token
     * @return array|WP_Error Response data
     */
    public function refresh_token($token) {
        return $this->request('auth/refresh', array(
            'method' => 'POST',
            'headers' => array(
                'Authorization' => 'Bearer ' . $token
            )
        ));
    }

    /**
     * Change password
     *
     * @param string $token Authentication token
     * @param string $newPassword New password
     * @return array|WP_Error Response data
     */
    public function change_password($token, $newPassword,$oldPassword) {
        return $this->request('api/v1/auth/change-password', array(
            'method' => 'POST',
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ),
            'body' => array(
                'newPassword' => $newPassword,
                'oldPassword' => $oldPassword // Assuming old password is the same as new for simplicity
            )
        ));
    }
    
    
   public function update_notification_settings($token, $data) {
    // $body = json_encode(array(
    //     'smsNotification' => filter_var($data['smsNotifications'], FILTER_VALIDATE_BOOLEAN),
    //     'newsUpdateNotification' => filter_var($data['emailNotifications'], FILTER_VALIDATE_BOOLEAN),
    // ));
        
    return $this->request('api/v1/user/profile/notification', array(
        'method' => 'PATCH',
        'headers' => array(
            'Authorization' => 'Bearer ' . $token,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json'
        ),
        'body' => $data
    ));



    // If WP_Error, return it directly
    if (is_wp_error($response)) {
        return $response;
    }

    // Standardize successful response
    if (isset($response['success']) && $response['success']) {
        return array(
            'success' => true,
            'data' => $response['data'] ?? null,
            'message' => $response['message'] ?? __('Notification settings updated successfully.', 'flexcore-server')
        );
    }

    // Standardize error response
    return array(
        'success' => false,
        'message' => $response['message'] ?? __('Failed to update notification settings', 'flexcore-server'),
        'data' => $response['data'] ?? null
    );
}
public function update_avatar_settings($token, $data) {
    // Prepare the request body
    $body = json_encode(array(
        'avatarId' => $data['avatarId'],
    ));

    // Make the request
    $response = $this->request('api/v1/user/profile/avatar', array(
        'method'  => 'PATCH',
        'headers' => array(
            'Authorization' => 'Bearer ' . $token,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json'
        ),
        'body' => $body
    ));

    // If WP_Error, return it directly
    if (is_wp_error($response)) {
        return $response;
    }

    // Standardize successful response
    if (isset($response['success']) && $response['success']) {
        return array(
            'success' => true,
            'data'    => $response['data'] ?? null,
            'message' => $response['message'] ?? __('Avatar updated successfully.', 'flexcore-server')
        );
    }

    // Standardize error response
    return array(
        'success' => false,
        'message' => $response['message'] ?? __('Failed to update avatar.', 'flexcore-server'),
        'data'    => $response['data'] ?? null
    );
}

    /**
     * Send registration request to API
     *
     * @param string $email
     * @param string $name
     * @param string $password
     * @return array|WP_Error
     */
    public function register_user($data) {
        return $this->request('api/v1/auth/register', array(
            'method' => 'POST',
            'body' => array(
                'fullName'     => $data['name'],
                'email'        => $data['email'],
                'password'     => $data['password'],
                'source'       => $data['register_source'],
                'campaignId'   => $data['campaign_id'],
                'utmCode'   => $data['utm_string'],
                'referralCode' => $data['referral_code'],
            )
        ));
    }
    
    /**
     * Refer friends
     *
     * @param string $token Authentication token
     * @param array $data Referral data
     * @return array|WP_Error Response data
     */
 public function refer_friends($token, $data) {
    // Prepare friends array in expected API format
    $friends = array_map(function($friend) {
        return array(
            'friendName'  => $friend['name'],
            'friendEmail' => $friend['email']
        );
    }, $data['friends']);

    // Prepare request body
    $body = json_encode(array(
        'friends' => $friends
    ));
error_log("Referral JSON Payload: " . $body);
    // Make the API call
    return $this->request('api/v1/referrals', array(
        'method'  => 'POST',
        'body'    => $body,
        'headers' => array(
            'Authorization' => 'Bearer ' . $token,
            'Content-Type'  => 'application/json'
        )
    ));
}

public function get_reward() {
    return $this->request('api/v1/rewards?limit=50', array(
        'method'  => 'GET',
        'headers' => array(           
            'Accept'        => 'application/json'
        )
    ));
}

public function get_reward_preview($reward_id) {
    error_log('Preview reward ID from API: ' . $reward_id);

    return $this->request("api/v1/rewards/{$reward_id}", array(
        'method'  => 'GET',
        'headers' => array(
            'Accept' => 'application/json'      
            
        )
    ));
}

   public function register($data) {
    $url = 'api/v1/auth/register';

    $postData = [
        'fullName'     => $data['name'],
        'email'        => $data['email'],
        'password'     => $data['password'],
        'source'       => $data['register_source'],
        'campaignId'   => $data['campaign_id'],
        'referralCode' => $data['referral_code'],
       
    ];

    $body = json_encode($postData);

    $response = $this->request($url, [
        'method'  => 'POST',
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ],
        'body'    => $body,
    ]);

    // Handle errors or decode response inside your request() method or here
    if (is_wp_error($response)) {
        return $response;
    }

    if (isset($response['success']) && $response['success']) {
        return [
            'success' => true,
            'data'    => $response['data'] ?? null,
            'message' => $response['message'] ?? __('Registration successful.', 'flexcore-server'),
        ];
    }

    return [
        'success' => false,
        'message' => $response['message'] ?? __('Registration failed.', 'flexcore-server'),
        'data'    => $response['data'] ?? null,
    ];
}
public function merged_register($data){
     $url = 'api/v2/auth/register';

    $body = json_encode($data);

    $response = $this->request($url, [
        'method'  => 'POST',
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ],
        'body'    => $body,
    ]);

    // Handle errors or decode response inside your request() method or here
   
        return $response;
    

    
}

public function validate_postal_code($postalCode) {
    $api_url = "https://www.onemap.gov.sg/api/common/elastic/search?searchVal="
        . urlencode($postalCode) . "&returnGeom=N&getAddrDetails=Y&pageNum=1";
error_log('Postal Code API URL: ' . $api_url);
    $response = wp_remote_get($api_url, [
        'timeout' => 15,
        'headers' => [
            'Authorization' => 'Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoxMDI1MCwiZm9yZXZlciI6ZmFsc2UsImlzcyI6Ik9uZU1hcCIsImlhdCI6MTc2NDU2ODI4MiwibmJmIjoxNzY0NTY4MjgyLCJleHAiOjE3NjQ4Mjc0ODIsImp0aSI6IjQxYzNiYWIwLTI5YzUtNDM0Yi1iODliLWJlNjQ0OWExODZmNCJ9.V99IhU3YnEbtUYsnHUIPXbghr0ubsF5uqVe3bs6sqbmpK8slH5XV7nekzOowr_cY9libJIG2SpO0hbsPHs3tpYlD2FOdHq1dwvuvzQVz3dt3Axwf50HlGteIPd4b7BE7CH8pKwq8HmiIML1pxft7FQQElnalqs2E7vGbozVebwGv2tjcZh25PizKX2412D7sRd-kGa85ZspXqNc695X508GfPlRD2oy5iOA8bjnP8Ren2TeRGLjY_mPB2c-6al3Ea4K0uktsG2v-C70F5QfVv97R-E-U0UMJqqPhjCSJl4XYEQoYFFn0yz34SXw-vB_SiJ6tKMGde2Oq9KfX60Qebg', // shorten here
            'Cookie'        => '_toffsuid=rB8FWGjSUwkaev7fA5clAg==',
            'User-Agent'    => 'Mozilla/5.0'
        ]
    ]);

    if (is_wp_error($response)) {
        error_log('Postal Code API Error: ' . $response->get_error_message());
        return $response;
    }
    

    error_log('HTTP Response Code: ' . wp_remote_retrieve_response_code($response));
    error_log('HTTP Response Message: ' . wp_remote_retrieve_response_message($response));
    error_log('Postal Code API Raw Response: ' . print_r($response, true));

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    error_log('Postal Code API Response: ' . print_r($data, true));

    return $data;
}


// public function validate_postal_code($postalCode) {
//     $api_url = "https://www.onemap.gov.sg/api/common/elastic/search?searchVal=" . urlencode($postalCode) . "&returnGeom=N&getAddrDetails=Y&pageNum=1";

//     $response = wp_remote_get($api_url, [
//         'timeout' => 10,
//         'headers' => [
//             'Authorization' => 'Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoxMDI1MCwiZm9yZXZlciI6ZmFsc2UsImlzcyI6Ik9uZU1hcCIsImlhdCI6MTc2NDU2ODI4MiwibmJmIjoxNzY0NTY4MjgyLCJleHAiOjE3NjQ4Mjc0ODIsImp0aSI6IjQxYzNiYWIwLTI5YzUtNDM0Yi1iODliLWJlNjQ0OWExODZmNCJ9.V99IhU3YnEbtUYsnHUIPXbghr0ubsF5uqVe3bs6sqbmpK8slH5XV7nekzOowr_cY9libJIG2SpO0hbsPHs3tpYlD2FOdHq1dwvuvzQVz3dt3Axwf50HlGteIPd4b7BE7CH8pKwq8HmiIML1pxft7FQQElnalqs2E7vGbozVebwGv2tjcZh25PizKX2412D7sRd-kGa85ZspXqNc695X508GfPlRD2oy5iOA8bjnP8Ren2TeRGLjY_mPB2c-6al3Ea4K0uktsG2v-C70F5QfVv97R-E-U0UMJqqPhjCSJl4XYEQoYFFn0yz34SXw-vB_SiJ6tKMGde2Oq9KfX60Qebg
//  ', // <--- Add your token
//             'Content-Type'  => 'application/json'
//         ]
//     ]);

//     if (is_wp_error($response)) {
//         error_log('Postal Code API Error: ' . $response->get_error_message());
//         return $response;
//     }

//     $body = wp_remote_retrieve_body($response);
//     $data = json_decode($body, true);

//     error_log('Postal Code API Response: ' . print_r($data, true));

//     return $data;
// }

}
