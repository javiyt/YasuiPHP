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
            $request = Yasui_Registry::get('request');
            $this->redirect($request->baseURL().'user/login');
        }
    }
}

