<?php
namespace utils;

/**
 * Utility class to generate hashes.
 *
 */
class Hash {
    public static function Sha256($data) {
        return hash('sha256', $data);
    }
}
