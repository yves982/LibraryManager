<?php
namespace views;
use stdClass;
use \views\utils\Renderer;
use \views\utils\Includes;

/**
 * Image view
 */
class Image implements IComponent {
    /** @var string $_src */
    private $_src;
    /** @var string $_format */
    private $_format;
    
    
    /**
     * Returns the HTML content for this view.
     * @return string
     */
    private function _getHtmlContent() {
        $context = new stdClass();
        $context->src = $this->_src;
        
        ob_start();
        include(Includes::$DOC_ROOT . '/views/templates/image/image.tpl.php');
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
    public function setSrc($src) {
        $this->_src = $src;
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
        $content = $this->_getHtmlContent();
        
        Renderer::render($content, $this->_format);
    }

}
