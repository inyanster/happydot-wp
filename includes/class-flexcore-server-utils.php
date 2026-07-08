<?php
/**
 * Utilities Class
 *
 * @package FlexCore_Server
 */

class FlexCore_Server_Utils {
    /**
     * Check if email is valid
     *
     * @param string $email Email to validate
     * @return bool
     */
    public static function is_valid_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Check password strength
     *
     * @param string $password Password to check
     * @return array Strength score and message
     */
    public static function check_password_strength($password) {
        $strength = 0;
        $message = '';

        if (strlen($password) < 12) {
            return array(
                'strength' => $strength,
                'message' => __('Password must be at least 12 characters long.', 'flexcore-server')
            );
        }

        if (preg_match('/[A-Z]/', $password)) {
            $strength++;
        }
        if (preg_match('/[a-z]/', $password)) {
            $strength++;
        }
        if (preg_match('/[0-9]/', $password)) {
            $strength++;
        }
        if (preg_match('/[^A-Za-z0-9]/', $password)) {
            $strength++;
        }

        switch ($strength) {
            case 1:
                $message = __('Very weak - Please add numbers, symbols and mixed case letters.', 'flexcore-server');
                break;
            case 2:
                $message = __('Weak - Please add more complexity.', 'flexcore-server');
                break;
            case 3:
                $message = __('Medium - Password is acceptable but could be stronger.', 'flexcore-server');
                break;
            case 4:
                $message = __('Strong - Good job!', 'flexcore-server');
                break;
            default:
                $message = __('Very weak - Please use a stronger password.', 'flexcore-server');
        }

        return array(
            'strength' => $strength,
            'message' => $message
        );
    }

    /**
     * Sanitize API response
     *
     * @param array|WP_Error $response API response
     * @return array
     */
    public static function sanitize_api_response($response) {
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => $response->get_error_message()
            );
        }

        if (!is_array($response)) {
            return array(
                'success' => false,
                'message' => __('Invalid response format.', 'flexcore-server')
            );
        }

        return wp_parse_args($response, array(
            'success' => false,
            'message' => '',
            'data' => array()
        ));
    }

    /**
     * Debug logging
     *
     * @param mixed $data Data to log
     * @return void
     */
    public static function debug_log($data) {
        if (WP_DEBUG === true && WP_DEBUG_LOG === true) {
            if (is_array($data) || is_object($data)) {
                error_log(print_r($data, true));
            } else {
                error_log($data);
            }
        }
    }

    /**
     * Get plugin option with default
     *
     * @param string $key Option key
     * @param mixed $default Default value
     * @return mixed
     */
    public static function get_option($key, $default = '') {
        return get_option('flexcore_' . $key, $default);
    }

    /**
     * Update plugin option
     *
     * @param string $key Option key
     * @param mixed $value Option value
     * @return bool
     */
    public static function update_option($key, $value) {
        return update_option('flexcore_' . $key, $value);
    }

    /**
     * Generate URL-friendly slug
     *
     * @param string $string String to slugify
     * @return string
     */
    public static function slugify($string) {
        return sanitize_title($string);
    }

    /**
     * Format date according to WordPress settings
     *
     * @param string $date Date string
     * @param string $format Optional format string
     * @return string
     */
    public static function format_date($date, $format = '') {
        if (empty($format)) {
            $format = get_option('date_format');
        }
        return date_i18n($format, strtotime($date));
    }

    /**
     * Convert date from display format to API format
     * No longer needed since display format is now YYYY-MM-DD
     *
     * @param string $date Date in YYYY-MM-DD format
     * @return string|false Date in YYYY-MM-DD format or false if invalid
     */
    public static function format_date_for_api($date) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return false;
        }
        
        list($year, $month, $day) = explode('-', $date);
        if (!checkdate($month, $day, $year)) {
            return false;
        }
        
        return $date; // Return as is since format is already correct
    }

    /**
     * Convert date from API format to display format
     * No longer needed since display format is now YYYY-MM-DD
     *
     * @param string $date Date in YYYY-MM-DD format
     * @return string|false Date in YYYY-MM-DD format or false if invalid
     */
    public static function format_date_for_display($date) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return false;
        }
        
        list($year, $month, $day) = explode('-', $date);
        if (!checkdate($month, $day, $year)) {
            return false;
        }
        
        return $date; // Return as is since format is already correct
    }

    /**
     * Clean user input
     *
     * @param string $input Input to clean
     * @return string
     */
    public static function clean_input($input) {
        return sanitize_text_field(wp_unslash($input));
    }

    /**
     * Generate random string
     *
     * @param int $length Length of string
     * @return string
     */
    public static function generate_random_string($length = 10) {
        return wp_generate_password($length, false);
    }

    /**
     * Check if string is JSON
     *
     * @param string $string String to check
     * @return bool
     */
    public static function is_json($string) {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }
    
    /**
     * Get current page URL
     *
     * @return string
     */
    public static function get_current_url() {
        return esc_url_raw((isset($_SERVER['HTTPS']) ? "https" : "http") . 
            "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    }
}
