<?php
namespace views;
use views\utils\Renderer;
use routes\Router;
use stdClass;

/**
 * A View for a set of selected Movies (does not contains movies's details)
 */
class MovieSet implements IComponent {
    /** @var string */
    private $_format;
    /** @var \models\MovieSet */
    private $_movies;
    
    /**
     * Generate JSON formatted content for the get action
     */
    private function _getJsonContent() {
        if(!empty($this->_movies) && count($this->_movies->ids) > 0) {
            $content = json_encode($this->_movies);
        }
        return $content;
    }
    
    /**
     * Renders the Get Action
     * @return string
     */
    private function _renderGet() {
        $content = '';
        switch ($this->_format) {
            case Router::FORMAT_HTML:
                Renderer::renderStatus(Router::HTTP_ERR_NOT_ACCEPTABLE, $this->_format);
                break;
            case Router::FORMAT_JSON:
                $content = $this->_getJsonContent();
                if($content == '') {
                    Renderer::renderStatus(Router::HTTP_OK_NOCONTENT, $this->_format);
                }
                break;
        }
        return $content;
    }
    
    /**
     * Sets the MovieSet for this view
     * @param \views\MovieSet $movies
     */
    public function setMovies(\models\MovieSet $movies) {
        $this->_movies = $movies;
    }
    
    /**
     * Renders this component
     * Note: setFormat must be called before.
     * @param string $action OPTIONAL
     */
    public function render($action = 'GET') {
        $content = '';
        switch ($action) {
            case 'GET':
                $content = $this->_renderGet();
                break;
        }
        Renderer::render($content, $this->_format);
    }

    /**
     * Sets format
     * @param string $format The answer's mime format
     */
    public function setFormat($format) {
        $this->_format = $format;
    }

}
