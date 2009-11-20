<?php
class Yasui_Database
{
    private $_dbAdapter = null;

    public function __construct($dbAdapter='MySQL')
    {
        if ($this->_dbAdapter == null) {
            if (!Yasui_Registry::exists('databaseConnection')) {
                require 'Yasui/Database/Driver/' . $dbAdapter . '.php';
                
                $adapter = 'Yasui_Database_Driver_' . $dbAdapter;
                Yasui_Registry::set('databaseConnection', new $adapter());
            }
        }
        $this->_dbAdapter = Yasui_Registry::get('databaseConnection');
    }

    public function dbAdapter()
    {
        return $this->_dbAdapter;
    }
}

