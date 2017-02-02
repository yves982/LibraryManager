<?php
namespace models\utils;
use Config;
use PDO;

/**
 * Simple utility class to manage connection
 */
class DALConnection {
    /**
     * @static
     * @return PDO
     */
    public static function getPdo() {
        $pdo = new PDO('mysql:host=' .Config::DB_HOST. ';port=' .Config::DB_PORT . ';dbname=' . Config::DB_NAME . ';charset=utf8', Config::DB_USER, Config::DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
