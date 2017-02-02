<?php
namespace views;
use views\utils\Includes;
use views\utils\Renderer;
use routes\Router;
use stdClass;

/**
 * Admin view
 * Sets headers and dump file to the output stream, that's all.
 */
class Admin implements IComponent {
    /** @var \models\Admin */
    private $_admin;
    /** @var string $_format */
    private $_format;
    
    /**
     * Set admin
     * @param \models\Admin $admin
     */
    public function setAdmin(\models\Admin $admin) {
        $this->_admin = $admin;
    }
    
    /**
     * Sets format
     * @param string $format The answer's mime format
     */
    public function setFormat($format) {
        $this->_format = $format;
    }
    
    /**
     * Get the Html content to render this view
     * @return string
     */
    private function _getHtmlContent() {
        $context = new stdClass();
        $context->admin = $this->_admin;
        ob_start();
        include(Includes::$DOC_ROOT . '/views/templates/AdminLogin.tpl.php');
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
    /**
     * Get the JSON content to render this view
     * @return string
     */
    private function _getJsonContent() {
        if(!empty($this->_admin) && !empty($this->_admin->login)) {
            $content = json_encode($this->_admin);
        } else {
            $answer = new stdClass();
            $answer->admin = null;
            $content = json_encode($answer);
        }
        return $content;
    }
    
    /**
     * Renders Get action
     * @return string content for the GET action
     */
    private function _renderGet() {
        $content = '';
        switch ($this->_format) {
            case Router::FORMAT_HTML:
                $content = $this->_getHtmlContent();
                break;
            case Router::FORMAT_JSON:
                $content = $this->_getJsonContent();
                break;
        }
        return $content;
    }
    
    /**
     * Renders this component
     * Note: setFormat must be called before.
     *  @param string $action OPTIONAL
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
}
