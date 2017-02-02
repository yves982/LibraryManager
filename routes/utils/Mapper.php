<?php
namespace routes\utils;
use stdClass;
use models\Movie;
use models\MovieSet;

/**
 * Classe utilitaire de transformations des donnees de requetes
 */
class Mapper {
    
    /**
     * maps data onto an object
     * @param stdClass $data the source object
     * @param object $obj the destination object
     */
    private static function _mapOnTo(stdClass $data, $obj) {
        $dataVars = get_object_vars($data);
        $objFields = get_object_vars($obj);
        
        foreach($dataVars as $field => $val) {
            if(isset($val) && array_key_exists($field, $objFields)) {
                $obj->{$field} = $val;
            }
        }
    }
    
    /**
     * map a stdClass to a movie
     * @param stdClass $data
     * @return Movie
     */
    public static function mapMovie(stdClass $data) {
        $movie = new Movie();
        self::_mapOnTo($data, $movie);
        return $movie;
    }
    
    /**
     * map a movie selection to a MovieSet
     * @param stdClass $data
     * @return MovieSet
     */
    public static function mapMovieSet(stdClass $data) {
        $movieSet = new MovieSet();
        self::_mapOnTo($data, $movieSet);
        return $movieSet;
    }
}
