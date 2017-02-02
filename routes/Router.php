<?php
namespace routes;
use controllers\MovieController;
use controllers\AdminController;
use controllers\ImageController;
use models\Movie;
use views\MovieList;
use views\utils\Renderer;
use models\utils\DALException;
use Exception;

/**
 * Router : Dipatch already broken requests
 * Note: in this simple case the broker is the index
 */
class Router {
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    
    const FORMAT_HTML = 'text/html';
    const FORMAT_JSON = 'application/json';
    
    const HTTP_OK = '200 OK';
    const HTTP_OK_NOCONTENT = '204 No Content';
    const HTTP_ERR_BAD_REQUEST = '400 Bad Request';
    const HTTP_ERR_NOT_FOUND = '404 Not Found';
    const HTTP_ERR_BAD_METHOD = '405 Bad Method';
    const HTTP_ERR_NOT_ACCEPTABLE = '406 Not Acceptable';
    const HTTP_ERR_INTERNAL_ERROR = '500 Internal Error';
    
    /** @var string $_uri */
    private $_uri;
    /** @var string $_method */
    private $_method;
    
    
    
    /**
     * Gets the best supported format
     * @param string $formats A semi column separated list of accepted mime formats
     * @return string the answer's content-type one of Router::FORMAT_* const
     */
    public static function getBestFormat($formats) {
        $bestFormat = self::FORMAT_HTML;
        if(strpos($formats, self::FORMAT_JSON) !== FALSE) {
            $bestFormat = self::FORMAT_JSON;
        }
        return $bestFormat;
    }
    
    /**
     * @return array an array of Route
     * @param MovieController $movieController
     * @param AdminController $adminController
     * @param ImageController $imageController
     * @param string $jsonData JSON encoded data for request's body
     */
    private function _getRoutes($movieController, $adminController, $imageController, $jsonData) {
        $routes = array(
            new Route(self::METHOD_GET, '@.+/movies/firstLetter/([a-zA-Z])/([0-9]+)/([0-9]+)$@i', array($movieController, 'listBlocByFirstLetter')),
            new Route(self::METHOD_GET, '@.+/movies/firstLetter/([a-zA-Z])/([0-9]+)$@i', array($movieController, 'listBlocByFirstLetter')),
            new Route(self::METHOD_GET, '@.+/movies/firstLetter/([a-zA-Z])$@i', array($movieController, 'listByFirstLetter')),
            new Route(self::METHOD_GET, '@.+/movie/([0-9]+)$@i', array($movieController, 'getDetails')),
            new Route(self::METHOD_POST, '@.+/admin/movie$@i', array($movieController, 'add'), $jsonData, array('\routes\utils\Mapper', 'mapMovie')),
            new Route(self::METHOD_PATCH, '@.+/admin/movie/([0-9]+)$@i', array($movieController, 'edit'), $jsonData, array('\routes\utils\Mapper', 'mapMovie')),
            new Route(self::METHOD_POST, '@.+/admin/movies/set$@i', array($movieController, 'select'), $jsonData, array('\routes\utils\Mapper', 'mapMovieSet')),
            new Route(self::METHOD_DELETE, '@.+/admin/movies/set/([0-9]+)$@i', array($movieController, 'removeAll')),
            new Route(self::METHOD_GET, '@.+/admin/login@i', array($adminController, 'login')),
            new Route(self::METHOD_POST, '@.+/admin/login$@', array($adminController, 'login'), $jsonData),
            new Route(self::METHOD_GET, '@.+/admin/logout$@', array($adminController, 'logout')),
            new Route(self::METHOD_POST, '@/admin/image/?$@', array($imageController, 'dataUri'))
        );
        return $routes;
    }
    
    /**
     * Defines Id field from route arguments
     * @param array $args Route arguments
     * @param type $entity the entity to add an id to
     */
    private function _defineId(array &$args, $entity) {
        $argsCnt = count($args);
        if(is_numeric($args[$argsCnt-1])) {
            $id = array_pop($args);
            $entity->id = $id;
        }
    }
    
    /**
     * Gets the prefferred mime type among accepted types.
     * @return string the accepted mime type for this request
     */
    private function _getPrefferedFormat() {
        $formats = filter_input(INPUT_SERVER, 'HTTP_ACCEPT');
        $format = self::getBestFormat($formats);
        return $format;
    }
    
    /**
     * Gets the arguments for the controller method to call
     * @param string $format The preffered accepted mime type
     * @param Route $route The route to retrieve arguments from
     * @return array an array with the preferred mime type followed by route URL arguments and any business entity read from the body
     */
    private function _getCallingArgs($format, Route $route) {
        $route->setUri($this->_uri);
        $routeArgs = $route->getArgs();
        $finalArgs = array_merge(array($format), $routeArgs);
        
        if($route->isMatched() && $route->jsonData != NULL) {
            $entity = $route->deserializeRequestData(json_decode($route->jsonData));
            $this->_defineId($finalArgs, $entity);
            $finalArgs[] = $entity;
        }
        return $finalArgs;
    }
    
    /**
     * Handle routes
     * @param Route[] $routes
     * @param boolean $handled true if a matching route was handled, false otherwise
     * @param boolean $argMatchedd OUT checks wether current request matched on route's args or not
     */
    private function _handleRoutes(array $routes, &$handled, &$argMatched) {
        $handled = false;
        $argMatched = false;
        foreach($routes as $route) {
            $route->setCheckedMethod($this->_method);
            $format = $this->_getPrefferedFormat();
            $finalArgs = $this->_getCallingArgs($format, $route);
            
            if($route->isMatched()) {
                call_user_func_array($route->callable, $finalArgs);
                $handled = true;
                break;
            } else if($route->hasArgMatch()) {
                $argMatched = true;
            }
        }
    }
    
    /**
     * Handles default routes and non existant one.
     * @param callable $defaultAction
     * @param array $defaultArgs argument values for defaultAction
     */
    private function _handleDefaultOrMissingRoute(callable $defaultAction,  array $defaultArgs) {
        $uriLen = strlen($this->_uri);
        $accept = filter_input(INPUT_SERVER, 'HTTP_ACCEPT');
        $format = Router::getBestFormat($accept);
        $finalArgs = array_merge(array($format), $defaultArgs);
        if(
                strpos($this->_uri, 'index.html') == $uriLen-10 
                || strpos($this->_uri, 'index.php?') !== FALSE
                || strpos($this->_uri, '/admin/movies') == $uriLen - 13
                || strpos($this->_uri, '/admin/movies/') == $uriLen - 14
                || strpos($this->_uri, '/movies') == $uriLen - 7
                || strpos($this->_uri, '/movies/') == $uriLen - 8
        ) {
            call_user_func_array($defaultAction, $finalArgs);
        } else {
            Renderer::renderStatus(self::HTTP_ERR_NOT_FOUND, $format);
        }
    }
    
    /**
     * Get Request data in JSON format.
     * @return string JSON formatted request's body
     */
    private function _getDataInJSON() {
        $contentTypeHeader = filter_input(INPUT_SERVER, 'CONTENT_TYPE');
        $contentType = explode(';', $contentTypeHeader)[0];
        $data = null;
       
        switch($contentType) {
            case 'application/json':
            case 'json':
                $data = file_get_contents('php://input');
                break;
            case 'application/x-www-form-urlencoded':
                $data = json_encode(filter_input_array(INPUT_POST));
                break;
        }
        return $data;
    }
    
    /**
     * Builds Contollers
     * @return array AdminController, MovieController
     */
    private function _getControllers(){
        $movie = new Movie();
        $listView = new MovieList();
        $movieView = new \views\Movie();
        $setView = new \views\MovieSet();
        $adminView = new \views\Admin();
        $imageView = new \views\Image();
        
        
        $movieController = new MovieController($listView, $movieView, $movie, $setView);
        $adminController = new AdminController($adminView);
        $imageController = new ImageController($imageView);
        return array($adminController, $movieController, $imageController);
    }
    
    /**
     * Handles a DALException
     * @param DALException $dex
     */
    private function _handleDalException(DALException $dex) {
        $accept = filter_input(INPUT_SERVER, 'HTTP_ACCEPT');
        $format = self::getBestFormat($accept);
        $status = self::HTTP_ERR_INTERNAL_ERROR;
        switch ($dex->getCode()) {
            case DALException::MISSING_REQUIRED_FIELD:
                $status = self::HTTP_ERR_BAD_REQUEST;
                break;
            case DALException::NOT_FOUND:
                $status = self::HTTP_ERR_NOT_FOUND;
                break;
        }
        Renderer::renderEx($dex, $status, $format);
    }
    
    /**
     * Handles an Exception
     * @param \Exception $ex
     */
    private function _handleGenericException(Exception $ex) {
        $accept = filter_input(INPUT_SERVER, 'HTTP_ACCEPT');
        $format = self::getBestFormat($accept);
        if(isset($ex->httpStatus)) {
            $status = $ex->httpStatus;
        } else {
            $status = self::HTTP_ERR_INTERNAL_ERROR;
        }
        Renderer::renderEx($ex, $status, $format);
    }
    
    /**
     * 
     * @param string $method one of METHOD_* constants of this class
     * @param string $uri
     */
    public function __construct($method, $uri) {
        $this->_uri = $uri;
        $this->_method = $method;
    }
    
    /**
     * Redirects the user to the given page
     * @param string $path Absolute path to the new page, starting with /.
     * @param string $scheme OPTIONAL http|https used to force http scheme
     */
    public static function redirect($path, $scheme = NULL) {
        $host = filter_input(INPUT_SERVER, 'HTTP_HOST');
        if(!isset($scheme)) {
            $scheme = filter_input(INPUT_SERVER, 'REQUEST_SCHEME');
        }
        header('HTTP/1.1 303 See Other');
        header('Location: ' .$scheme. '://' .$host. $path );
        flush();
        
    }
    
    /**
     * Routes current request to the appropriate controller and method
     * Notes: this method does not supports x-www-form-urlencoded data
     * @see php://input
     */
    public function route() {
        try {
            $data = $this->_getDataInJSON();
            $handled = false;
            $argMatched = false;
            list($adminController, $movieController, $imageControllers) = $this->_getControllers();

            $routes = $this->_getRoutes($movieController, $adminController, $imageControllers, $data);
            $this->_handleRoutes($routes, $handled, $argMatched);
            
            if(!$handled && !$argMatched) {
                $this->_handleDefaultOrMissingRoute(array($movieController, 'listByFirstLetter'), array(''));
            } else if (!$handled && $argMatched) {
                $accept = filter_input(INPUT_SERVER, 'HTTP_ACCEPT');
                $format = self::getBestFormat($accept);
                Renderer::renderStatus(Router::HTTP_ERR_BAD_METHOD, $format);
            }
        } catch(DALException $dex) {
            $this->_handleDalException($dex);
        }
        catch(Exception $ex) {
            $this->_handleGenericException($ex);
        }
    }

    

}
