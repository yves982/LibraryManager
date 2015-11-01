<?php
namespace routes;
use stdClass;

/**
 * A simple class to represent an action path
 */
class Route {
    /** @var string one of Router::METHOD_* constants. */
    public $method;
    /** @var string a regular expression pattern. */
    public $pattern;
    /** @var callable A Callable to handle this Route. */
    public $callable;
    /** @var string A valid JSON encoded string for request's body */
    public $jsonData;
    /** @var boolean $_argMatched */
    private $_argMatched;
    /** @var boolean $_methodMatched */
    private $_methodMatched;
    /** @var callable $_deserializer */
    private $_deserializer;
    
    /** @var string the uri to check against */
    private $_uri;
    /** @var array an array with the route arguments as defined in the pattern */
    private $_args;
    
    /**
     * Initializes an instance.
     * @param string $method
     * @param string $pattern
     * @param callable $callable
     * @param string $jsonData a request's JSON encoded data
     * @param callable $deserializer delegate to handle data conversion from stdClass to a model
     */
    public function __construct($method, $pattern, $callable, $jsonData = NULL, callable $deserializer = NULL) {
        $this->method = $method;
        $this->pattern = $pattern;
        $this->callable = $callable;
        $this->jsonData = $jsonData;
        $this->_deserializer = $deserializer;
    }
    
    /**
     * Set the method to check
     * @param string $checkedMethod HTTP method used
     */
    public function setCheckedMethod($checkedMethod) {
        $this->_methodMatched = $this->method == $checkedMethod;
    }
    
    /**
     * Deserialize request data
     * @param stdClass $data request's data
     * @return mixed some model
     */
    public function deserializeRequestData(stdClass $data) {
        if(!empty($this->_deserializer)) {
            return call_user_func($this->_deserializer, $data);
        } else {
            return $data;
        }
    }
    
    /**
     * Checks wether this route was matched or not.
     * @return boolean
     */
    public function isMatched() {
        return $this->_argMatched && $this->_methodMatched;
    }
    
    /**
     * Checks wether this route's arguments were matched or not by current request.
     * @return boolean
     */
    public function hasArgMatch() {
        return $this->_argMatched;
    }
    
    /**
     * Checks wether this route's method was matched or not by current request.
     * @return boolean
     */
    public function hasMethodMatch() {
        return $this->_methodMatched;
    }
    
    /**
     * Sets the uri to extract arguments from
     * @param string $uri
     */
    public function setUri($uri) {
        $this->_uri = $uri;
    }
    
    /**
     * Gets Route's arguments for a given uri.
     * @return array an array of matches for this Route's pattern.
     */
    public function getArgs() {
        if($this->_args == null) {
            $this->_argMatched = preg_match($this->pattern, $this->_uri, $this->_args) == 1;
        }
        if($this->_args != null) {
            array_shift($this->_args);
        }
        
        return $this->_args;
    }
}
