<?php
namespace controllers;
use views\Image;
use routes\Router;
use views\utils\Renderer;
use \finfo;

/**
 * Old browser fallback controller to upload images
 *
 */
class ImageController {
    const FILE = 'image';
    const ERR = 'error';
    const TMP_PATH = 'tmp_name';
    
    /** @var \views\Image */
    private $_imageView;
    
    public function __construct(Image $imageView) {
        $this->_imageView = $imageView;
    }
    
    /**
     * Ensures the request accepted type is acceptable
     * @param string $format
     */
    private function _ensuresAcceptableRequest($format) {
        if($format !== Router::FORMAT_HTML) {
            Renderer::renderStatus(Router::HTTP_ERR_NOT_ACCEPTABLE, Router::FORMAT_HTML);
        }
    }
    
    /**
     * Handles upload errors
     */
    private function _handleErrors() {
        switch ($_FILES[self::FILE][self::ERR]) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                $ex = new \RuntimeException('no file sent', 16, NULL);
                Renderer::renderEx($ex, Router::HTTP_ERR_BAD_REQUEST, Router::FORMAT_HTML);
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $ex = new \RuntimeException('file is too big', 15, NULL);
                Renderer::renderEx($ex, Router::HTTP_ERR_BAD_REQUEST, Router::FORMAT_HTML);
                break;
            default:
                $ex = new \RuntimeException('unknown error', 17, NULL);
                Renderer::renderEx($ex, Router::HTTP_ERR_BAD_REQUEST, Router::FORMAT_HTML);
                break;
        }
    }
    
    /**
     * Makes sure the upload request is in proper format and there's no error
     */
    private function _ensuresNoError() {
        if(
            !isset($_FILES[self::FILE])
            || !isset($_FILES[self::FILE][self::ERR])
            || is_array($_FILES[self::FILE][self::ERR])
        ) {
          Renderer::renderStatus(Router::HTTP_ERR_BAD_REQUEST, Router::FORMAT_HTML);
        } else {
            $this->_handleErrors();
        }
    }
    
    /**
     * Makes sure the uploaded file fits in server's size policy
     */
    private function _ensuresSizeInRange() {
        $maxSize = ini_get('upload_max_filesize');
        $kPos = stripos($maxSize, 'k');
        $mPos = stripos($maxSize, 'm');
        
        if($kPos !== FALSE){
            $maxSize = substr($maxSize, 0, $kPos) * 1024;
        } else if($mPos !== FALSE) {
            $maxSize = substr($maxSize, 0, $mPos) * 1024 * 1024;
        }
        
        $currentSize = filesize($_FILES[self::FILE][self::TMP_PATH]);
        
        if($currentSize > $maxSize) {
            $ex = new \RuntimeException('file is too big', 15, NULL);
            Renderer::renderEx($ex, Router::HTTP_ERR_BAD_REQUEST, Router::FORMAT_HTML);
        }
    }
    
    
    private function _getProperMimeType(finfo $finfo) {
        $infos = $finfo->file($_FILES[self::FILE][self::TMP_PATH]);
        if($infos === FALSE) {
            $ex = new \RuntimeException('File is not a recognized image format. Accepted types are jpg, png, gif.', 18, NULL);
            Renderer::renderEx($ex, Router::HTTP_ERR_BAD_REQUEST, Router::FORMAT_HTML);
        }
        
        $knownTypes = array(
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        );
        
        $ext = array_search($infos, $knownTypes);
        
        if($ext === FALSE) {
            Renderer::renderEx($ex, Router::HTTP_ERR_BAD_REQUEST, Router::FORMAT_HTML);
        } else {
            return $knownTypes[$ext];
        }
    }
    
    public function dataUri($format) {
        $this->_ensuresAcceptableRequest($format);
        $this->_ensuresNoError();
        $this->_ensuresSizeInRange();
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $this->_getProperMimeType($finfo);

        try {
            $tmpFile = $_FILES[self::FILE][self::TMP_PATH];
            $handle = fopen($tmpFile, 'rb');
            $rawContent = fread($handle, filesize($tmpFile));
            $encodedContent = base64_encode($rawContent);
            $dataUri = 'data:' .$mime.';base64,' .$encodedContent;
            fclose($handle);
            $this->_imageView->setSrc($dataUri);
            $this->_imageView->render();
        } catch(Exception $ex) {
            Renderer::renderStatus(Router::HTTP_ERR_INTERNAL_ERROR, Router::FORMAT_HTML);
        }
    }
}
