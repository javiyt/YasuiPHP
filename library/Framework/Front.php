<?php
final class Framework_Front
{
    private $router;
    private $preAction = array();
    private $postAction = array();

    public function __construct ()
    {
        $this->router = Framework_Router::getRoute();
        $this->view = Framework_View::getInstance();
        $this->addPostAction('renderView');
    }

    public function addPreAction ($action)
    {
        $this->preAction[] = $action;
    }

    public function addPostAction ($action)
    {
        $this->postAction[] = $action;
    }

    public function forward($controller='index',$action='index')
    {
        $this->router['controller'] = $controller;
        $this->router['action'] = $action;
        $this->dispatch();
    }

    public function dispatch ()
    {
        $class = ucfirst(strtolower($this->router['controller'])).'Controller';
        $fileclass = APPLICATION_ROOT.CONTROLLER_ROOT.$class.'.php';
        if (file_exists($fileclass)) {
            require $fileclass;
            $controller = new $class;
            
            foreach ($this->preAction as $action) {
                $this->execute($controller,$action);
            }

            $action = ucfirst(strtolower($this->router['action'])).'Action';
            $controller->setAction($this->router['action']);
            $this->execute($controller,$action);

            $this->addPostAction('renderLayout');
            
            foreach($this->postAction as $action) {
                $this->execute($controller,$action);
            }
        } else {
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
            if (file_exists(LAYOUT_ROOT.'404.phtml')) {
                $this->view->show('404.phtml');
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
                $this->view->show('404.phtml');
            } else {
                echo 'Not Found';
            }
        }
    }
}

