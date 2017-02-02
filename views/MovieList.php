<?php
namespace views;
use stdClass;
use views\utils\Includes;
use views\utils\Renderer;
use routes\Router;
use Generator;

/**
 * MovieList view
 * Sets headers and dump file to the output stream, that's all.
 */
class MovieList implements IRightAwareView{
    /** @var \models\Movie[] $_movies Generator of \models\Movie */
    private $_movies;
    /** @var int */
    private $_moviesCnt;
    /** @var callable */
    private $_moviesCntCb;
    /** @var IComponent[] $_components  */
    private $_components;
    /** @var string $_format */
    private $_format;
    /** @var boolean $_hasChangeRights */
    private $_hasChangeRights;
    
    /**
     * Initializes an instance
     */
    public function __construct() {
        $this->_hasChangeRights = false;
    }
    
    /**
     * Get the Html content
     * @return string
     */
    private function _getHtmlContent() {
        $context = new stdClass();
        $context->moviesBlocs = $this->_movies;
        $context->components = $this->_components;
        foreach($this->_components as $comp) {
            $comp->setFormat($this->_format);
        }
        $context->hasChangeRights = $this->_hasChangeRights;
        ob_start();
        include(Includes::$DOC_ROOT . '/views/templates/MovieList.tpl.php');
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
    /**
     * Gets the JSON content
     * @return string
     */
    private function _getJsonContent() {
        $data = new stdClass();
        $data->movies = array();
        $data->hasChangeRights = $this->_hasChangeRights;
        foreach($this->_movies as $movie) {
            $data->movies[] = $movie;
        }
        call_user_func($this->_moviesCntCb);
        $data->cnt = (int)$this->_moviesCnt;
        $content = json_encode($data);
        return $content;
    }
    
    /**
     * Set movies
     * @param Generator $movies Generator of \models\Movie
     */
    public function setMovies(Generator $movies) {
        $this->_movies = $movies;
    }
    /**
     * Sets the number of movies.
     * @param int $moviesCnt
     */
    public function setMoviesCnt($moviesCnt) {
        $this->_moviesCnt = $moviesCnt;
    }
    
    public function setMoviesCntCb($callback) {
        $this->_moviesCntCb = $callback;
    }
    
    /**
     * Set components
     * @param IComponent[] $components
     */
    public function setComponents(array $components) {
        $this->_components = $components;
    }
    /**
     * Set format
     * @param string $format
     */
    public function setFormat($format) {
        $this->_format = $format;
    }
    /**
     * Sets the user rights
     * @param boolean $hasChangeRights
     */
    public function setChangeRights($hasChangeRights) {
        $this->_hasChangeRights = $hasChangeRights;
    }
    
    /**
     * Render this view
     */
    public function render() {
        $content = '';
        switch($this->_format) {
            case Router::FORMAT_HTML:
                $content = $this->_getHtmlContent();
                break;
            case Router::FORMAT_JSON:
                $content = $this->_getJsonContent();
                break;
        }
        Renderer::render($content, $this->_format);
        
        
    }
}
