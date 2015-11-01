<?php
namespace models;
use PDO;
use stdClass;
use models\utils\DALConnection;
use models\utils\DALException;

/**
 * Movie
 * A persistance class following ActiveRecord pattern
 */
class Movie {
    /** @var int $id */
    public $id;
    /** @var string $title */
    public $title;
    /** @var string $author */
    public $author;
    /** @var int $year */
    public $year;
    /** @var string $description */
    public $description;
    /** @var string $image image Path */
    public $image;
    /** @var string $content The URL of the movie */
    public $content;
    /** @var boolean $_isValid */
    private $_isValid;
    
    /**
     * Extracts a Movie from a database record
     * @param stdClass $movieRecord an object representing a movie in database
     * @return Movie
     * @static
     */
    private static function _mapMovie(stdClass $movieRecord) {
        $movie = new Movie();
        $movie->id = (int)$movieRecord->id;
        $movie->title = $movieRecord->titre;
        $movie->year = (int)$movieRecord->annee;
        $movie->content = $movieRecord->contenu;
        $movie->description = $movieRecord->description;
        $movie->author = $movieRecord->realisateur;
        $movie->image = $movieRecord->image;
        return $movie;
    }
    
    /**
     * Validates current instance
     * @return boolean true if current instance is valid, false otherwise.
     */
    private function _validate() {
        if(!$this->_isValid) {
            $this->_isValid = !empty($this->title) && is_string($this->title)
            && !empty($this->description) && is_string($this->description)
            && !empty($this->year) && is_int($this->year +1);
        }
        return $this->_isValid;
    }
    
    /**
     * 
     * @param \PDOStatement $statement the result set to yield results from
     * @param int $dataLength OUT number of records yielded
     * @static
     */
    private static function _yieldData(\PDOStatement $statement, &$dataLength) {
        $data = $statement->fetch(PDO::FETCH_OBJ);
        while($data !== FALSE){
            $movie = self::_mapMovie($data);
            $dataLength++;
            yield $movie;
            $data = $statement->fetch(PDO::FETCH_OBJ);
        }
    }
    
    /**
     * Find a group of $maxCnt movies after $mastLoadedId by their title's first letter
     * @param string $firstLetter
     * @param int $maxCnt max movies count for this request
     * @param int $moviesCnt OUT total movies count for this letter
     * @param int $blocNum OPTIONNEL
     * @static
     */
    public static function findGroupByFirstLetter($firstLetter, $maxCnt, &$moviesCnt, $blocNum = 1) {
        $startInd = ($blocNum -1) * $maxCnt;
        $pdo = DALConnection::getPdo();
        $statement = $pdo->prepare('CALL recuperer_bloc_film_parPremiereLettre(:premiereLettre, :startInd, :endInd, @eltCnt)');
        $statement->bindParam(':premiereLettre', $firstLetter, PDO::PARAM_STR);
        $statement->bindParam(':startInd', $startInd,PDO::PARAM_INT);
        $statement->bindParam(':endInd', $maxCnt, PDO::PARAM_INT);
        $statement->execute();
        $dataLength=0;
        $data = self::_yieldData($statement, $dataLength);
        foreach($data as $record) {
            yield $record;
        }
        $statement->closeCursor();
        if($dataLength > 0) {
            $statement2 = $pdo->prepare('SELECT @eltCnt as "Cnt"');
            $statement2->execute();
            $moviesCnt = $statement2->fetchColumn();
            $statement2->closeCursor();
        }
    }
    
    /**
     * Generator which yields serialized json representation of the next 10 records.
     * @param string $firstLetter
     * @static
     * @example <pre>
     * do {
     * header('Transfer-Encoding: chuncked;');
     * flush();
     * $data = Movie::findByFirstLetter('n');
     * fwrite('php://output', $data);
     * fflush('php://output');
     * flush();
     * }while($data != NULL);
     * fclose('php://output')
     * </pre>
     */
    public static function findByFirstLetter($firstLetter) {
        $pdo = DALConnection::getPdo();
        $statement = $pdo->prepare('CALL recuperer_film_parPremiereLettre(:premiereLettre)');
        $statement->bindParam(':premiereLettre', $firstLetter, PDO::PARAM_STR);
        $statement->execute();
        
        $data = $statement->fetch(PDO::FETCH_OBJ);
        while($data !== FALSE){
            $movie = self::_mapMovie($data);
            yield $movie;
            $data = $statement->fetch(PDO::FETCH_OBJ);
        }
    }
    
    /**
     * Finds a film by its id
     * @param int $id
     * @return Movie
     * @static
     */
    public static function findById($id) {
        $pdo = DALConnection::getPdo();
        $statement = $pdo->prepare('CALL recuperer_film(:id)');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $movie = $statement->fetch(PDO::FETCH_OBJ);
        if($movie != NULL ) {
            return self::_mapMovie($movie);
        } else {
            return null;
        }
    }
    
    /**
     * Saves a movie in database.
     */
    public function create() {
        if(!$this->_validate()) {
            throw new DALException(__CLASS__, __METHOD__, null, DALException::MISSING_REQUIRED_FIELD);
        }
        $pdo = DALConnection::getPdo();
        $statement = $pdo->prepare('CALL creer_film(@id, :titre, :realisateur, :annee, :description, :contenu, :image)');
        //$statement->bindParam(':id', $this->id, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT);
        $statement->bindParam(':titre', $this->title, PDO::PARAM_STR);
        $statement->bindParam(':realisateur', $this->author, PDO::PARAM_STR);
        $statement->bindParam(':annee', $this->year, PDO::PARAM_INT);
        $statement->bindParam(':description', $this->description, PDO::PARAM_STR);
        $statement->bindParam(':contenu', $this->content, PDO::PARAM_STR);
        $statement->bindParam(':image', $this->image, PDO::PARAM_STR);
        $statement->execute();
        $statement->closeCursor();
        
        $statement2 = $pdo->prepare('SELECT @id as "Id"');
        $statement2->execute();
        $this->id = $statement2->fetchColumn();
        $statement2->closeCursor();
    }
    
    /**
     * Updates a film
     * Note: Its id must be set
     */
    public function update() {
        $pdo = DALConnection::getPdo();
        $statement = $pdo->prepare('CALL mettreAJour_film(:id, :titre, :realisateur, :annee, :description, :contenu, :image)');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT, 8);
        $statement->bindParam(':titre', $this->title, PDO::PARAM_STR);
        $statement->bindParam(':realisateur', $this->author, PDO::PARAM_STR);
        $statement->bindParam(':annee', $this->year, PDO::PARAM_INT);
        $statement->bindParam(':description', $this->description, PDO::PARAM_STR);
        $statement->bindParam(':contenu', $this->content, PDO::PARAM_STR);
        $statement->bindParam(':image', $this->image, PDO::PARAM_STR);
        $statement->execute();
    }
    
}
