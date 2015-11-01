<?php
namespace controllers;
use models\Movie;
use models\MovieSet;
use views\MovieList;
use views\MovieSet as MovieSetView;
use views\utils\Renderer;
use routes\Router;
use utils\auth\SessionManager;
use Exception;


/**
 * Controller for movies operations
 *
 */
class MovieController {
    /** @var views\Movie $_movieView */
    private $_movieView;
    /** @var Movie $_movie */
    private $_movie;
    /** @var Generator $_movies a Movie Generator */
    private $_movies;
    /** @var int number of available movies */
    private $_moviesCnt;
    /** @var MovieList */
    private $_listView;
    /** @var \views\MovieSet */
    private $_setView;
    
    /** @var array $_components Array of \views\IComponents */
    private $_components;
    
    /**
     * Set wether user has change rights or not.
     * @param mixed $view Some business view with a setChangeRights method
     */
    private function _setChangeRights($view) {
        $hasRights = SessionManager::hasRights();
        $view->setChangeRights($hasRights);
    }
    
    /**
     * Clean a movie's title so that it fits filename rules.
     * @param string $title
     */
    private function _cleanTitle($title) {
        return str_replace(array('\'', '\`', '\&', '+', '#', '@', ',', '!', '_', ':'), '', $title);
    }
    
    /**
     * Get fileType, extension, fileName infos out of a new Movie
     * @param Movie $movie
     * @return string[] fileType, extension, fileName
     */
    private function _getFileInfos(Movie $movie) {
        $firstSemiColumn = strpos($movie->image, ';');
        $fileType = substr($movie->image, 5, $firstSemiColumn-5);
        $ext = substr($fileType, strpos($fileType, '/')+1);
        $title = $this->_cleanTitle($movie->title);
        $fileName = implode('', array_map('ucfirst', explode(' ', $title))) .'_'. $movie->year . '.' . $ext;
        return array($fileType, $ext, $fileName);
    }
    
    /**
     * Writes movie image in case it's a dataURL.
     * @param Movie $movie
     */
    private function _createImageFromDataURL(Movie $movie) {
        if(!isset($movie->image, $movie->title, $movie->year)) {
            return;
        }
        if(strcasecmp(substr($movie->image, 0, 5), 'data:') == 0) {
            list($fileType, $ext, $fileName) = $this->_getFileInfos($movie);
            
            try {
                $fileHandle = fopen($this->_getImageFullPath('/resources/images/' . $fileName), 'wb+');
                $firstComma = strpos($movie->image, ',');
                $content = base64_decode(str_replace(' ', '+', substr($movie->image, $firstComma)));
                fwrite($fileHandle, $content);
                $movie->image = '/resources/images/' . $fileName;
            } finally {
                if(isset($fileHandle)) {
                    fclose($fileHandle);
                }
            }
        }
    }
    
    private function _getImageFullPath($relativePath) {
        // PROD :  return '/var/www/html/SimpleLibrary' . $relativePath;
        return __DIR__ . $relativePath;
    }
    
    /**
     * Rename a movie's image according to its title and year.
     * @param \models\Movie $movie
     */
    private function _renameImage(Movie $movie) {
        $ext = substr($movie->image, strripos($movie->image, '.'));
        $newFileName = $this->_cleanTitle(
            $movie->title
        ). '_' . $movie->year . $ext;
        $newFileBasePath = '/resources/images/' . $newFileName;
        $newFileFullPath =  $this->_getImageFullPath($newFileBasePath);
        $oldImage = str_replace('%20', ' ', $movie->image);
        $success = rename($this->_getImageFullPath($oldImage), $newFileFullPath);
        if(!$success) {
            throw new Exception('Echec au renommage de ' . $movie->image);
        }
        $movie->image = $newFileBasePath;
    }
    
    /**
     * Render details view.
     * @param string $format mime type accepted by the client
     * @param \models\Movie the Movie to render
     */
    private function _renderDetails($format, Movie $movie) {
        $this->_movieView->setMovie($movie);
        $this->_movieView->setFormat($format);
        $this->_setChangeRights($this->_movieView);
        $this->_movieView->render();
    }
    
    /**
     * Prepare a movie for edition (grabs required elements when they're not being updated.
     * @param Movie $oldMovie persistant old version of current movie
     * @param Movie $movie Current Movie to prepare
     */
    private function _prepareMovie(Movie $oldMovie, Movie $movie) {
        $needRename = isset($movie->title) || isset($movie->year);
        
        if(!isset($movie->title)){
            $movie->title = $oldMovie->title;
        }
        
        if(!isset($movie->year)) {
            $movie->year = $oldMovie->year;
        }
        
        if(isset($movie->image)) {
            $this->_createImageFromDataURL($movie);
        } else {
            $movie->image = $oldMovie->image;
        }
        
        if($needRename) {
            $this->_renameImage($movie);
        }
    }
    
    /**
     * Initializes an instance
     * @param MovieList $listView Movies list view OPTIONAL used when viewing a list of movies
     * @param views\Movie $movieView A view for a single movie OPTIONAL
     * @param Movie $movie OPTIONAL used to change a movie
     * @param views\MovieSet $setView OPTIONAL
     * @param IComponent[] $components OPTIONAL
     */
    public function __construct(MovieList $listView = NULL, \views\Movie $movieView = NULL, Movie $movie = NULL, MovieSetView $setView = NULL, array $components = array()) {
        $this->_movie = $movie;
        $this->_listView = $listView;
        $this->_movieView = $movieView;
        $this->_setView = $setView;
        $this->_components = $components;
    }
    
    /**
     * Stores a movie
     * @param string $format the accepted mime type
     * @param Movie $movie
     */
    public function add($format, Movie $movie) {
        SessionManager::ensuresAuth();
        try {
            
            $this->_createImageFromDataURL($movie);

            $movie->create();
            $this->_movieView->setChangeRights(true);
            $this->_movieView->setFormat($format);
            $this->_movieView->setMovie($movie);
            $this->_movieView->render();
        } catch(Exception $ex) {
            if(!empty($movie) && isset($movie->image)) {
                unlink($this->_getImageFullPath($movie->image));
            }
        }
    }
    
    /**
     * Select one or more movies
     * @param string $format
     * @param MovieSet $movieSet
     */
    public function select($format, MovieSet $movieSet) {
        $movieSet->create();
        $this->_setView->setFormat($format);
        $this->_setView->setMovies($movieSet);
        $this->_setView->render();
    }
    
    /**
     * Remove one or more movies from database
     * @param string $format
     * @param int $selectionId
     */
    public function removeAll($format, $selectionId) {
        SessionManager::ensuresAuth();
        $movieSet = MovieSet::findById($selectionId);
        $movieSet->delete();
        Renderer::renderStatus(Router::HTTP_OK, $format);
    }
    
    public function edit($format, Movie $movie) {
        SessionManager::ensuresAuth();
        $oldMovie = Movie::findById($movie->id);
        
        $this->_prepareMovie($oldMovie, $movie);
        
        $movie->update();
        $updatedMovie = Movie::findById($movie->id);
        unset($movie);
        $this->_renderDetails($format, $updatedMovie);
    }
    
    public function listByFirstLetter($format, $firstLetter) {
        $this->_movies = Movie::findByFirstLetter($firstLetter);
        $this->_listView->setMovies($this->_movies);
        $this->_listView->setComponents($this->_components);
        $this->_listView->setFormat($format);
        $this->_setChangeRights($this->_listView);
        $this->_listView->render();
    }
    
    public function onMoviesRead() {
        $this->_listView->setMoviesCnt($this->_moviesCnt);
    }
    
    public function listBlocByFirstLetter($format, $firstLetter, $maxCnt, $lastLoadedId=NULL) {
        $this->_moviesCnt = 0;
        $this->_movies = Movie::findGroupByFirstLetter($firstLetter, $maxCnt, $this->_moviesCnt, $lastLoadedId);
        $this->_listView->setMovies($this->_movies);
        $this->_listView->setMoviesCntCb(array($this, 'onMoviesRead'));
        $this->_listView->setComponents($this->_components);
        $this->_listView->setFormat($format);
        $this->_setChangeRights($this->_listView);
        $this->_listView->render();
    }
    
    public function getDetails($format, $id) {
        $this->_movie = Movie::findById($id);
        if($this->_movie == NULL) {
            Renderer::renderStatus(Router::HTTP_ERR_NOT_FOUND, $format);
        }
        $this->_renderDetails($format, $this->_movie);
    }
}
