<?php
class Framework_Auth_Adapter_DB extends Framework_Auth_Abstract
{
    private $_dbAdapter = null;

    public function __construct()
    {
        if ($this->_dbAdapter == null) {
            $this->_dbAdapter = new Framework_Database();
        }
        if ($this->_session == null) {
            $this->_session = Framework_Session::start('auth');
        }
    }

    public function authenticate($identity, $credential)
    {
        if ($this->_identity == null || $this->_credential == null) {
            return false;
        }

        $auth = $this->_dbAdapter->dbAdapter()->getOne($this->_location,$this->_identity,array($this->_identity => $identity, $this->_credential => $this->_getCrypCredential($credential)));

        if (is_array($auth)) {
            $this->_session->identity = $auth[$this->_identity];
            return true;
        } else {
            return false;
        }
    }
}

