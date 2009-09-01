<?php
class IndexController extends Framework_Controller {

    public function indexAction()
    {
        $auth = Framework_Auth::getInstance();
        $authAdapter = $auth->getAdapter('DB');
        $authAdapter->authenticate('mth@mthweb.org','javi');
        $authAdapter->isAuthenticate();
    }
}

