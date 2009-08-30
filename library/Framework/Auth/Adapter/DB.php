<?php
class Framework_Auth_Adapter_DB extends Framework_Auth_Abstract
{
    private $_dbAdapter = null;
    private $_session = null;

    public function __construct()
    {
        if ($this->_dbAdapter == null) {
            $this->_dbAdapter = new Framework_Database();
        }
        if ($this->_session == null) {
            $this->_session = Framework_Session::start('auth','database');
        }
    }
}

