<?php
class IndexController extends Yasui_Controller {

    public function indexAction()
    {
    }

    public function preDispatch()
    {
        $auth = Yasui_Auth::getInstance();
        $authAdapter = $auth->getAdapter('DB');
        if (!$authAdapter->isAuthenticate()) {
            $this->redirect($this->_request()->baseURL() . 'user/login');
        }
    }
}

