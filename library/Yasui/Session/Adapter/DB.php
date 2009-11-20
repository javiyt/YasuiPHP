<?php
require 'Yasui/Session/Abstract.php';

class Yasui_Session_Adapter_DB extends Yasui_Session_Abstract
{
    private $_tableConfig = array();
    private $_dbAdapter = null;

    public function __construct($tableConfig=array())
    {
        if ($this->_dbAdapter == null) {
            $this->_dbAdapter = new Yasui_Database();
        }
        if (count($tableConfig) == 0) {
            $config = Yasui_Registry::get('config');
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
        $this->gc(0);
    }

    public function read($session_id)
    {
        $tmp = $this->_dbAdapter->dbAdapter()->getOne($this->_tableConfig['table'], $this->_tableConfig['data'], array($this->_tableConfig['sid'] => $session_id));
        return $tmp[$this->_tableConfig['data']];
    }

    public function write($session_id, $session_data)
    {
        if ($this->_dbAdapter->dbAdapter()->exists($this->_tableConfig['table'], $this->_tableConfig['sid'], $session_id)) {
            $update = array(
                $this->_tableConfig['modified'] => 'UNIX_TIMESTAMP()',
                $this->_tableConfig['lifetime'] => '1800',
                $this->_tableConfig['data'] => $session_data
            );
            $this->_dbAdapter->dbAdapter()->insert($this->_tableConfig['table'], array($this->_tableConfig['sid'] => $session_id), $update);

        } else {
            $insert = array(
                $this->_tableConfig['sid'] => $session_id,
                $this->_tableConfig['modified'] => 'UNIX_TIMESTAMP()',
                $this->_tableConfig['lifetime'] => '1800',
                $this->_tableConfig['data'] => $session_data
            );
            $this->_dbAdapter->dbAdapter()->insert($this->_tableConfig['table'], $insert);
        }
    }

    public function destroy($session_id)
    {
        $this->_dbAdapter->dbAdapter()->delete($this->_tableConfig['table'], array($this->_tableConfig['sid'] => $session_id));
    }

    public function gc($max_lifetime)
    {
        $where = $this->_tableConfig['lifetime'] . ' + ' . $this->_tableConfig['modified'] . ' < NOW()';
        $this->_dbAdapter->dbAdapter()->delete($this->_tableConfig['table'], $where);
    }

}

