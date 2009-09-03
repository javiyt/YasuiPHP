<?php
class Savant3_Plugin_menuAuth extends Savant3_Plugin
{
    protected $authAdapter = null;

    public function menuAuth()
    {
        if ($this->authAdapter != null && $this->authAdapter->isAuthenticate()) {
            return 'Bienvenido';
        } else {
            require_once 'Framework/View/Helpers/Savant3_Plugin_generateURL.php';
            $generate = new Savant3_Plugin_generateURL();
            return $generate->generateURL(array('action' => 'login','controller' => 'user'),'No Connected');
        }
    }
}

