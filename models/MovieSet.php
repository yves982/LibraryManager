<?php
namespace models;
use models\utils\DALConnection;
use models\utils\DALException;
use PDO;
use PDOStatement;
use stdClass;

/**
 * A collection of movies
 *
 */
class MovieSet {
    /** @var int[] an array of integer */
    public $ids;
    /** @var int */
    public $id;
    
    /**
     * Gets a MovieSet from a statement
     * @param PDOStamtement $statement (should contains only one id for multiple idFilm)
     * @return MovieSet
     */
    private static function _mapFromStatement(PDOStatement $statement) {
        $movieSet = new MovieSet();
        $data = $statement->fetch(PDO::FETCH_OBJ);
        
        if($data !== FALSE) {
            $movieSet->id = $data->id;
        }
        while($data !== FALSE) {
            $movieSet->ids[] = $data->idFilm;
            $data = $statement->fetch(PDO::FETCH_OBJ);
        }
        
        return $movieSet;
    }
    
    /**
     * Finds a MovieSet by its id
     * @param int $id
     * @return MovieSet
     */
    public static function findById($id) {
        $pdo = DALConnection::getPdo();
        $statement = $pdo->prepare('CALL recuperer_films_liste(:id)');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $movieSet = self::_mapFromStatement($statement);
        
        if(empty($movieSet->id)) {
            throw new DALException(__CLASS__, __METHOD__, array('id' => $id), DALException::NOT_FOUND );
        }
        
        return $movieSet;
    }
    
    /**
     * Creates a collection of movies
     * 
     */
    public function create() {
        $pdo = DALConnection::getPdo();
        $statement = $pdo->prepare('CALL creer_films_liste(@id,:ids)');
        $ids = implode(',', $this->ids);
        $statement->bindParam(':ids', $ids, PDO::PARAM_STR);
        $statement->execute();
        
        $statement2 = $pdo->prepare('SELECT @id as "Id"');
        $statement2->execute();
        $this->id = $statement2->fetchColumn();
        $statement2->closeCursor();
    }
    
    /**
     * Deletes a collection of movies and all associated movies
     */
    public function delete() {
        $pdo = DALConnection::getPdo();
        $statement = $pdo->prepare('CALL supprimer_films_liste(:id)');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);
        $statement->execute();
    }
}
