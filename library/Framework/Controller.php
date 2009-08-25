<?php
abstract class Framework_Controller
{
    private $renderFormat = 'html';
    private $output;
    protected $view;
    protected $action;

    public function __construct ()
    {
        $this->view = Framework_View::getInstance();
    }

    protected function redirect($url)
    {
        header('Location: '.$url);
        exit();
    }

    protected function renderJSON($data)
    {
        $this->renderFormat = 'json';
        $this->output = json_encode($data);
    }

    public function renderView()
    {
        if ($this->renderFormat == 'html') {
            $name = str_replace('controller','',strtolower(get_class($this)));
            $this->view->template()->addTemplatePath(APPLICATION_ROOT.'views'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR);
            $this->view->content = $this->view->template()->fetch($this->action.'.phtml');
        } else {
            echo $this->output;
        }
    }

    public function renderLayout()
    {
        if ($this->renderFormat == 'html') {
            $this->view->template()->display('indexLayout.phtml');
        }
    }

    public function setAction($action)
    {
        $this->action = $action;
    }
}

