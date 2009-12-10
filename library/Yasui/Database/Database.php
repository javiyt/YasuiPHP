<?php
class Yasui_Database
{
    private $_dbAdapter = null;

    public function __construct($dbAdapter='MySQL')
    {
        if ($this->_dbAdapter == null) {
            if (!Yasui_Registry::exists('databaseConnection')) {
				$config = Yasui_Registry::get('config');
				if (isset($config->database['driver']) && file_exists(dirname(__FILE__) . '/Driver/' . $config->database['driver'] . '.php')) {
					$dbAdapter = $config->database['driver'];
				}
				
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

