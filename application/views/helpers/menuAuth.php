<?php
class Yasui_View_Helper_menuAuth extends Yasui_View_Helper
{
    protected $authAdapter = null;

    public function menuAuth()
    {
        require_once 'Yasui/View/Helper/generateURL.php';
        $generate = new Yasui_View_Helper_generateURL();

        if ($this->authAdapter != null && $this->authAdapter->isAuthenticate()) {
            return $generate->generateURL(array('action' => 'logout','controller' => 'user'), 'Desconectar');
        } else {
            return $generate->generateURL(array('action' => 'login','controller' => 'user'),'Conectar').' '.$generate->generateURL(array('action' => 'register','controller' => 'user'), 'Registrar');
        }
    }
}

