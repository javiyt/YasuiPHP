<?php
abstract class Framework_Controller
{
    private $_renderFormat = 'html';
    private $_output;
    protected $_view;
    protected $_action;

    public function __construct ()
    {
        $this->_view = Framework_View::getInstance();
    }

    protected function redirect($url)
    {
        header('Location: '.$url);
        exit();
    }

    protected function renderJSON($data)
    {
        $this->_renderFormat = 'json';
        $this->_output = json_encode($data);
    }

    public function renderView()
    {
        if ($this->_renderFormat == 'html') {
            $name = str_replace('controller','',strtolower(get_class($this)));
            $this->_view->template()->addTemplatePath(VIEWS_ROOT.$name.DIRECTORY_SEPARATOR);
            $this->_view->content = $this->_view->template()->fetch($this->_action.'.phtml');
        } else {
            echo $this->_output;
        }
    }

    public function renderLayout()
    {
        if ($this->_renderFormat == 'html') {
            $this->_view->template()->display('indexLayout.phtml');
        }
    }

    public function setAction($action)
    {
        $this->_action = $action;
    }

    public function init()
    {
    }

    public function preDispatch()
    {
    }
}

