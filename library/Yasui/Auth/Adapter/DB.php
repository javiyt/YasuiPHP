<?php
require 'Yasui/Auth/Abstract.php';

class Yasui_Auth_Adapter_DB extends Yasui_Auth_Abstract
{
    private $_dbAdapter = null;

    public function __construct()
    {
        if ($this->_dbAdapter == null) {
            $this->_dbAdapter = new Yasui_Database();
        }
        if ($this->_session == null) {
            $this->_session = Yasui_Session::start('auth');
        }
    }

    public function authenticate($identity, $credential, $extra = array())
    {
        if ($this->_identity == null || $this->_credential == null) {
            return false;
        }

        $get = array_merge(array($this->_identity => $identity, $this->_credential => $this->_getCrypCredential($credential)), $extra);

        $auth = $this->_dbAdapter->dbAdapter()->getOne($this->_location, $this->_identity, $get);

        if (is_array($auth)) {
            $this->_session->identity = $auth[$this->_identity];
            return true;
        } else {
            return false;
        }
    }
}

