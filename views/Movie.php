<?php
namespace views;
use stdClass;
use views\utils\Includes;
use views\utils\Renderer;
use routes\Router;

/**
 * Movie view
 * Sets headers and dump file to the output stream, that's all.
 */
class Movie implements IComponent, IRightAwareView {
    /** @var models\Movie $_movie */
    private $_movie;
    /** @var string $_format */
    private $_format;
    /** @var boolean $_hasChangeRights */
    private $_hasChangeRights;
    
    /**
     * Renders Get action
     * @return string content for the GET action
     */
    private function _renderGet() {
        $content = '';
        switch($this->_format) {
            case Router::FORMAT_HTML:
                $context = new stdClass();
                $context->movie = $this->_movie;
                ob_start();
                include(Includes::$DOC_ROOT . '/views/templates/Movie.tpl.php');
                $content = ob_get_contents();
                ob_end_clean();
                break;
            case Router::FORMAT_JSON:
                $content = json_encode($this->_movie);
                break;
        }
        return $content;
    }
    
    private function _renderAdd() {
        $content = '';
        switch($this->_format) {
            case Router::FORMAT_HTML:
                break;
            case Router::FORMAT_JSON:
                break;
        }
        return $content;
    }
    
    /**
     *  Sets the user rights
     * @param boolean $hasChangeRights
     */
    public function setChangeRights($hasChangeRights){
        $this->_hasChangeRights = $hasChangeRights;
    }
    
    /**
     * Set movie
     * @param \models\Movie $movie
     */
    public function setMovie(\models\Movie $movie) {
        $this->_movie = $movie;
    }
    /**
     * Sets format
     * @param string $format The answer's mime format
     */
    public function setFormat($format) {
        $this->_format = $format;
    }
    
    /**
     * Renders this component
     * Note: setFormat must be called before.
     * @param string $action OPTIONAL
     */
    public function render($action = 'GET') {
        $content = '';
        switch($action) {
            case 'GET':
                $content = $this->_renderGet();
                break;
            case 'ADD':
                $content = $this->_renderAdd();
                break;
        }
        Renderer::render($content, $this->_format);
        
    }
}
