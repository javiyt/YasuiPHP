<?php
/**
 * Yasui Framework
 *
 * @category   Yasui
 * @package    Yasui_Front
 * @license    GNU Lesser General Public License (LGPL) version 2.1
 */

final class Yasui_Front
{
    /**
     * Store the Yasui_Router object
     * @var object
     * @access private
     */
    private $_router;
    /**
     * Actions to execute before the main action is executed
     * @var array
     * @access private
     */
    private $_preAction = array();
    /**
     * Actions to execute after the main action is executed
     * @var array
     * @access private
     */
    private $_postAction = array();
    /**
     * Store the Yasui_View object
     * @var object
     * @access private
     */
    private $_view;

    /**
     * Constructor of the class
     * @access public
     */
    public function __construct ()
    {
        //Get the Yasui_Router from the registry
        $this->_router = Yasui_Registry::get('router');
        //Instantiate the Yasui_View object
        $this->_view = Yasui_View::getInstance();
        //Adds init action to execute before the main action
        $this->addPreAction('init');
        //Adds preDispatch action to execute before the main action
        $this->addPreAction('preDispatch');
        //Adds the renderView action to execute after the main action
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

    public function forward($controller='index', $action='index')
    {
        $this->_router->controller = $controller;
        $this->_router->action = $action;
        $this->dispatch();
    }

    public function dispatch ()
    {
        $class = ucfirst(strtolower($this->_router->controller)) . 'Controller';
        //If module is null application is working in the default controller directory
        if ($this->_router->module == null) {
            $fileclass = APPLICATION_ROOT . CONTROLLER_ROOT . $class . '.php';
        } else {
            //Module exists, so the controller have to been located into the folder
            $fileclass = APPLICATION_ROOT . CONTROLLER_ROOT . $this->_router->module . DIRECTORY_SEPARATOR . $class . '.php';
        }

        if (file_exists($fileclass)) {
            require $fileclass;
            $controller = new $class;
            
            foreach ($this->_preAction as $action) {
                $this->execute($controller,$action);
            }

            $action = ucfirst(strtolower($this->_router->action)) . 'Action';
            $controller->setAction($this->_router->action);
            $this->execute($controller, $action);

            $this->addPostAction('renderLayout');
            
            foreach($this->_postAction as $action) {
                $this->execute($controller, $action);
            }
        } else {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
            if (file_exists(LAYOUT_ROOT . '404.phtml')) {
                $this->_view->display('404.phtml');
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
                $this->_view->display('404.phtml');
            } else {
                echo 'Not Found';
            }
        }
    }
}

