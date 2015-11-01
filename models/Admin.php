<?php
namespace models;
use PDO;
use stdClass;
use models\utils\DALConnection;
use models\utils\DALException;
/**
 * Admin
 * A persistance class following ActiveRecord pattern
 */
class Admin {
    /** @var int $id */
    public $id;
    /** @var string $name */
    public $name;
    /** @var string $login */
    public $login;
    /** @var string $password */
    public $password;
    /** @var string $email */
    public $email;
    
    /**
     * Extracts an Admin from a database record
     * @param stdClass $adminRecord an object representing an admin in database
     * @return Admin
     */
    private static function _mapAdmin(stdClass $adminRecord) {
        $admin = new Admin();
        $admin->id = (int)$adminRecord->id;
        $admin->name = $adminRecord->nom;
        $admin->login = $adminRecord->login;
        $admin->password = $adminRecord->mdp;
        $admin->email = $adminRecord->email;
        return $admin;
    }
    
    /**
     * Finds an admin by its login
     * @param string $login
     * @return Admin
     * @static
     */
    public static function findByLogin($login) {
        $pdo = DALConnection::getPdo();
        $statement = $pdo->prepare('CALL recuperer_admin_parLogin(:login)');
        $statement->bindParam(':login', $login, PDO::PARAM_STR);
        $statement->execute();
        $admin = $statement->fetch(PDO::FETCH_OBJ);
        if($admin != NULL ) {
            return self::_mapAdmin($admin);
        } else {
            throw new DALException(__CLASS__, __METHOD__, array('login' => $login), DALException::NOT_FOUND);
        }
    }
}
