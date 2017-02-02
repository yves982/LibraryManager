<?php
namespace utils;

/**
 * Array utils class
 */
class ArrayUtils {
    /**
     * Returns a string representation of a key value pair.
     * @param string $key
     * @param string $value
     * @return String
     */
    public static function hashJoin($key, $value) {
        return $key .':'.$value;
    }
}
