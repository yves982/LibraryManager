<?php
namespace views\utils;
use routes\Router;
use Exception;
use stdClass;

/**
 * Simple class to set headers and flush output content
 *
 * @author yves
 */
class Renderer {
    
    /**
     * Renders content according to format and set HTTP status code.
     * @param string $content
     * @param string $format The answer's Content-Type {@see \routes\Router::getBestFormat}
     * @param string $status one of \routes\Router::HTTP_* constants | OPTIONAL defaults to Renderer::HTTP_OK
     */
    public static function render($content, $format, $status = Router::HTTP_OK) {
        header('HTTP/1.1 ' .$status);
        if($status == Router::HTTP_OK) {
            header('Content-Type: ' . $format);
        }
        header('Content-Length: ' .strlen($content));
	      header('Access-Control-Allow-Origin: http://lib.ylalanne.ovh');
        flush();
        ob_start();
        echo $content;
        ob_end_flush();
        flush();
        exit();
    }
    
    /**
     * Render HTTP status for the given format
     * @param string $status one of \routes\Router::HTTP_* constants
     * @param string $format one of \routes\Router::FORMAT_* constants
     */
    public static function renderStatus($status, $format) {
        header('HTTP/1.1 '.$status);
        header('Content-Type: ' .$format);
        
        switch($format) {
            case Router::FORMAT_JSON:
                header('Content-Length: ' .strlen('{}'));
		            header('Access-Control-Allow-Origin: http://lib.ylalanne.ovh');
                flush();
                ob_start();
                echo '{}';
                ob_end_flush();
                flush();
                break;
            case Router::FORMAT_HTML:
		            header('Access-Control-Allow-Origin: http://lib.ylalanne.ovh');
                flush();
                break;
        }
        exit();
    }
    
    /**
     * Renders an exception
     * @param Exception $ex
     * @param int $httpStatus one of Router::HTTP_ERR_* const
     * @param string $format one of \routes\Router::FORMAT_* constants
     */
    public static function renderEx(Exception $ex, $httpStatus, $format) {
        header('HTTP/1.1 '.$httpStatus);
        header('Content-Type: ' .$format);
        
        switch($format) {
            case Router::FORMAT_JSON:
                $content = json_encode($ex);
                header('Content-Length: ' . strlen($content));
		            header('Access-Control-Allow-Origin: http://lib.ylalanne.ovh');
                flush();
                ob_start();
                echo $content;
                ob_end_flush();
                flush();
                break;
            case Router::FORMAT_HTML:
                $context = new stdClass();
                $context->ex = $ex;
                $context->httpStatus = $httpStatus;
		            header('Access-Control-Allow-Origin: http://lib.ylalanne.ovh');
                ob_start();
                include(Includes::$DOC_ROOT . '/views/templates/Error.tpl.php');
                ob_end_flush();
                flush();
                break;
        }
        
        exit();
    }
}
