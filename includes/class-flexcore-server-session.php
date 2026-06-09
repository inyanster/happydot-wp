<?php
/**
 * Session Management Class
 *
 * @package FlexCore_Server
 */

class FlexCore_Server_Session {
    /**
     * Session token key
     */
    const TOKEN_KEY = 'flexcore_auth_token';
    
    /**
     * Session user profile key
     */
    const PROFILE_KEY = 'flexcore_user_profile';
    const MEMBERSHIP_KEY = 'flexcore_membership';

    /**
     * Get the current authentication token
     *
     * @return string|null The authentication token or null if not set
     */
    public static function get_token() {
        return isset($_SESSION[self::TOKEN_KEY]) ? $_SESSION[self::TOKEN_KEY] : null;
    }

    /**
     * Set the authentication token
     *
     * @param string $token The authentication token to store
     */
    public static function set_token($token) {
        if (!session_id()) {
            session_start();
        }
        $_SESSION[self::TOKEN_KEY] = $token;
    }

    /**
     * Clear the authentication token
     */
    public static function clear_token() {
        if (!session_id()) {
            session_start();
        }
        unset($_SESSION[self::TOKEN_KEY]);
    }

    /**
     * Check if user is authenticated
     *
     * @return boolean True if authenticated, false otherwise
     */
    public static function is_authenticated() {
        return self::get_token() !== null;
    }

    /**
     * Initialize session handling
     */
    public static function init() {
        if (!session_id()) {
            session_start();
        }
        
        // Set secure cookie parameters
        $secure = is_ssl();
        $httponly = true;
        
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => 'Strict'
        ]);
    }

    /**
     * Set user profile data in session
     *
     * @param array $profile User profile data
     */
    public static function set_user_profile($profile) {
        if (!session_id()) {
            session_start();
        }
        error_log("Setting user profile in session: " . print_r($profile, true));
        $_SESSION[self::PROFILE_KEY] = $profile;
    }

    /**
     * Get user profile data from session
     *
     * @return array|null User profile data or null if not set
     */
    public static function get_user_profile() {
        return isset($_SESSION[self::PROFILE_KEY]) ? $_SESSION[self::PROFILE_KEY] : null;
    }

    
     /**
     * Set user membership data in session
     *
     *
     */
    public static function set_user_membership_status($status) {
        if (!session_id()) {
            session_start();
        }
        
        $_SESSION[self::MEMBERSHIP_KEY] = $status;
    }

    /**
     * Get user membership data from session
     *
     * null or int will be retuned
     */
    public static function get_user_membership_status() {
        return isset($_SESSION[self::MEMBERSHIP_KEY]) ? $_SESSION[self::MEMBERSHIP_KEY] : null;
    }
    /**
     * Clear user profile data
     */
    public static function clear_user_profile() {
        if (!session_id()) {
            session_start();
        }
        unset($_SESSION[self::PROFILE_KEY]);
    }

    /**
     * Clear all session data
     */
    public static function clear_all() {
        if (!session_id()) {
            session_start();
        }
        self::clear_token();
        self::clear_user_profile();
    }
}
