<?php
/**
 * Yasui Framework
 *
 * @category   Yasui
 * @package    Yasui_Controller
 * @license    GNU Lesser General Public License (LGPL) version 2.1
 */

abstract class Yasui_Controller
{
    /**
     * Default render view format
     * @var string
     * @access private
     */
    private $_renderFormat = 'html';
    /**
     * Contains the output to show, after render view
     * @var string
     * @access private
     */
    private $_output;
    /**
     * Contains the Yasui_View object
     * @var object
     * @access protected
     */
    protected $_view;
    /**
     * Contains the name of the action to execute into the controller class
     * @var string
     * @access protected
     */
    protected $_action;
    /**
     * Contains the Yasui_Request object
     * @var object
     * @access protected
     */
    protected $_request = null;
    /**
     * Contains the Yasui_Router object
     * @var object
     * @access protected
     */
    protected $_router = null;

    /**
     * Constructor of the class
     * @access public
     */
    public function __construct()
    {
        //Instantiate the Yasui_View
        $this->_view = Yasui_View::getInstance();
    }

    protected function _request()
    {
        if ($this->_request == null) {
            //Get the Yasui_Request object from the registry
            $this->_request = Yasui_Registry::get('request');
        }

        return $this->_request;
    }

    protected function _router()
    {
        if ($this->_router == null) {
            //Get the Yasui_Router object from the registry
            $this->_router = Yasui_Registry::get('router');
        }
        return $this->_router;
    }

    /**
     * Redirect to another url
     * @param string $url
     * @@access protected
     */
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit();
    }

    /**
     * Render output in JSON format
     * @param string $data
     * @access protected
     */
    protected function renderJSON($data)
    {
        //Avoid to render the output into html
        $this->_renderFormat = 'json';
        //Set the output
        $this->_output = json_encode($data);
    }

    /**
     * Renders the output view
     * @access public
     */
    public function renderView()
    {
        //If output format is HTML renders the view script associated to executed action
        if ($this->_renderFormat == 'html') {
            //Get the directory where is the view script into the views folder
            $name = str_replace('controller', '', strtolower(get_class($this)));
            $this->_view->addTemplatePath(VIEWS_ROOT . $name . DIRECTORY_SEPARATOR);
            //Set the content view with the actual view script
            $this->_view->content = $this->_view->fetch($this->_action . VIEWS_EXTENSION);
        } else {
            //If the output is another format, not HTML, returns the output
            echo $this->_output;
        }
    }

    /**
     * Render the final output with layout
     * @access public
     */
    public function renderLayout()
    {
        //If the render output format is html, render the final view with layout
        if ($this->_renderFormat == 'html') {
            echo $this->_view;
        }
    }

    /**
     * Set the action executed
     * @param string $action
     * @access public
     */
    public function setAction($action)
    {
        $this->_action = $action;
    }

    /**
     * Init function to override into the final controller class
     * @access public
     */
    public function init()
    {
    }

    /**
     * preDispatch function to override into the final controller class
     * @access public
     */
    public function preDispatch()
    {
    }
}

