<?php
require 'Framework/Router.php';
require 'Framework/View.php';

final class Framework_Front
{
    private $_router;
    private $_preAction = array();
    private $_postAction = array();
    private $_view;

    public function __construct ()
    {
        $this->_router = Framework_Router::getRoute();
        $this->_view = Framework_View::getInstance();
        $this->addPreAction('init');
        $this->addPreAction('preDispatch');
        $this->addPostAction('renderView');
    }

    public function addPreAction ($action)
    {
        $this->_preAction[] = $action;
    }

    public function addPostAction ($action)
    {
        $this->_postAction[] = $action;
    }

    public function forward($controller='index',$action='index')
    {
        $this->_router['controller'] = $controller;
        $this->_router['action'] = $action;
        $this->dispatch();
    }

    public function dispatch ()
    {
        $class = ucfirst(strtolower($this->_router['controller'])).'Controller';
        $fileclass = APPLICATION_ROOT.CONTROLLER_ROOT.$class.'.php';
        if (file_exists($fileclass)) {
            require $fileclass;
            $controller = new $class;
            
            foreach ($this->_preAction as $action) {
                $this->execute($controller,$action);
            }

            $action = ucfirst(strtolower($this->_router['action'])).'Action';
            $controller->setAction($this->_router['action']);
            $this->execute($controller,$action);

            $this->addPostAction('renderLayout');
            
            foreach($this->_postAction as $action) {
                $this->execute($controller,$action);
            }
        } else {
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
            if (file_exists(LAYOUT_ROOT.'404.phtml')) {
                $this->_view->show('404.phtml');
            } else {
                echo 'Not Found';
            }
        }
    }

    private function execute($controller,$action)
    {
        if (method_exists($controller,$action)) {
            call_user_func(array($controller,$action));
        } else {
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
            if (file_exists(LAYOUT_ROOT.'404.phtml')) {
                $this->_view->show('404.phtml');
            } else {
                echo 'Not Found';
            }
        }
    }
}

