<?php
class Framework_Session_Adapter_DB extends Framework_Session_Abstract
{
    private $_tableConfig = array();
    private $_dbAdapter = null;

    public function __construct($tableConfig=array())
    {
        if ($this->_dbAdapter == null) {
            $this->_dbAdapter = new Framework_Database();
        }
        if (count($tableConfig) == 0) {
            $config = Framework_Registry::get('config');
            $tableConfig = $config->session;
        }

        $this->setConfig($tableConfig);
    }

    public function setConfig($tableConfig=array())
    {
        if (!isset($tableConfig['table'])) {
            throw new Exception('Configuration table not found');
        }
        if (!isset($tableConfig['sid'])) {
            $tableConfig['sid'] = 'id';
        }
        if (!isset($tableConfig['modified'])) {
            $tableConfig['modified'] = 'modified';
        }
        if (!isset($tableConfig['lifetime'])) {
            $tableConfig['lifetime'] = 'lifetime';
        }
        if (!isset($tableConfig['data'])) {
            $tableConfig['data'] = 'data';
        }

        $this->_tableConfig = $tableConfig;
    }

    public function close()
    {

    }

    public function read($session_id)
    {

    }

    public function write($session_id,$session_data)
    {

    }

    public function destroy($session_id)
    {

    }

    public function gc($max_lifetime)
    {
        //$this->_dbAdapter->delete($this->tableConfig['table'],);
    }

}

