<?php
namespace models\utils;
use Exception;
/**
 * DAL Exception
 *
 */
class DALException extends Exception {
    const NOT_FOUND = 1;
    const MISSING_REQUIRED_FIELD = 2;
    
    /**
     * Initializes an instance.
     * @param string $callingClass the fully qualified name of the calling class
     * @param string $callingMethod the name of the calling method
     * @param array $seekParams a hash in the form name => value 
     * @param int $code One of DALException::* constants OPTIONNEL (Defaut: DALException::NOT_FOUND)
     * @param Exception $previous The inner exception if any OPTIONNEL
     */
    public function __construct($callingClass, $callingMethod, $seekParams, $code = self::NOT_FOUND, $previous = NULL) {
        switch($code) {
            case self::NOT_FOUND:
                $pairs = array_map(array('\utils\ArrayUtils', 'hashJoin'), array_keys($seekParams), $seekParams);
                $message = 'data not found: ' .$callingClass. ' in (' .$callingMethod. ') params: {' 
                        .implode(',', $pairs). '}.';
                break;
            default:
                $message = 'Unknown error in ' .$callingClass. '(method: ' .$callingMethod. ')!';
                break;
        }
        parent::__construct($message, $code, $previous);
    }
}
