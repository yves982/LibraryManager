<?php
namespace utils\auth;

/**
 * Manages Session
 *
 */
class SessionManager {
    private static $started = false;

    /**
     * Starts or resumes a session
     * @static
     */
    private static function _start() {
        session_start();
        self::$started = true;
    }
    /**
     * Authenticate a given Administrator
     * @param \models\Admin $admin
     * @static
     */
    public static function authenticate(\models\Admin $admin) {
        if(!self::$started) {
            self::_start();
        }
        $_SESSION['User'] = serialize($admin);
    }
    
    /**
     * Ensures current request has a valid session.
     * @static
     * @throws AuthException in case we're not in a valid session
     */
    public static function ensuresAuth() {
        if(!self::$started) {
            self::_start();
        }
        
        if(!array_key_exists('User', $_SESSION)) {
            throw new AuthException("User is not authenticated");
        }
    }
    
    /**
     * Grabs the authenticated user.
     * @static
     * @return \models\Admin The authenticated administrator if any, null otherwise
     */
    public static function getAuthUser() {
        if(!self::$started) {
            self::_start();
        }
        if(array_key_exists('User', $_SESSION)) {
            return unserialize($_SESSION['User']);
        }
    }
    
    /**
     * Check wether the user has admin rights or not
     * @return boolean true if user is an administrator false otherwise
     */
    public static function hasRights() {
        if(!self::$started) {
            self::_start();
        }
        
        return array_key_exists('User', $_SESSION);
    }
    
    /**
     * Destroys session
     * @static
     */
    public static function destroy() {
        if(!self::$started) {
            self::_start();
        }
        $cookieParams = session_get_cookie_params();
        session_unset();
        session_destroy();
        session_write_close();

        setcookie(session_name(), '', time()-42000, 
                $cookieParams['path'], $cookieParams['domain'],
                $cookieParams['secure'], $cookieParams['httponly']
        );
        flush();
        self::$started = false;
    }
}
