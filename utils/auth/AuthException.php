<?php
namespace utils\auth;
use Exception;
/**
 * AuthException
 *
 */
class AuthException extends Exception{
    const NOT_AUTHENTICATED = 0;
    
    /**
     * Initializes an instance.
     * @param string $message
     * @param int $code one of AuthException::* constants OPTIONAL
     * @param Exception $previous OPTIONAL
     */
    public function __construct($message, $code = self::NOT_AUTHENTICATED, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}
