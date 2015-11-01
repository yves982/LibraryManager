<?php
namespace controllers;
use models\Admin;
use utils\Hash;
use utils\auth\SessionManager;
use routes\Router;

/**
 * Controller for the Admin ressource
 *
 */
class AdminController implements IChildController {
    /** @var \models\Admin $_admin */
    private $_admin;
    /** @var \views\Admin */
    private $_adminView;
    
    /**
     * Initializes an instance
     * @param \views\Admin $adminView
     */
    public function __construct(\views\Admin $adminView) {
        $this->_adminView = $adminView;
    }
    
    /**
     * Logs an administrator in.
     * @param string $format the accepted answer's format
     * @param stdClass $loginData ( a class with login, password and access fields)
     * @return boolean true in case of successfull login
     */
    public function login($format, $loginData = NULL) {
        if(isset($loginData) && !empty($loginData->login)) {
            $hashedPassword = Hash::Sha256($loginData->password);
            $this->_admin = Admin::findByLogin($loginData->login);
            $success = false;
            if($this->_admin->password == $hashedPassword) {
                $success = true;
                SessionManager::authenticate($this->_admin);
                unset($this->_admin->password, $this->_admin->id, $this->_admin->email);
                $this->_adminView->setAdmin($this->_admin);
            }
            
            $this->_adminView->setFormat($format);
            $this->_adminView->render();
        } else {
            $admin = SessionManager::getAuthUser();
            
            if(!empty($admin)) {
                $this->_adminView->setAdmin($admin);
            }
            $this->_adminView->setFormat($format);
            $this->_adminView->render();
        }
        
    }
    
    /**
     * Logs an administrator out.
     * @param string $formats A string containing accepted answer's formats
     */
    public function logout($formats) {
        SessionManager::destroy();
        $format = Router::getBestFormat($formats);
        if(isset($this->_admin)) {
            unset($this->_admin->id, $this->_admin->password, $this->_admin->email, $this->_admin->login);
            switch($format) {
                case Router::FORMAT_HTML:
                    Router::redirect('/movies/firstLetter/a', 'https');
                    break;
                case Router::FORMAT_JSON:

                    $this->_adminView->setFormat($format);
                    $this->_adminView->setAdmin($this->_admin);
                    $this->_adminView->render();
                    break;
            }
        }
        else {
            $this->_adminView->setFormat($format);
            $this->_adminView->render();
        }
        
    }
   /**
     * Get an array of IComponent to render
     * @return \views\IComponent[]
     */
    public function getComponents() {
        $this->_admin = SessionManager::getAuthUser();
        if(isset($this->_admin)) {
            $this->_adminView->setAdmin($this->_admin);
        }
        return array($this->_adminView);
    }
}
