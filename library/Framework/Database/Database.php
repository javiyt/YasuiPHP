<?php
class Framework_Database
{
    private $_dbAdapter = null;

    public function __construct($dbAdapter='MySQL')
    {
        if ($this->_dbAdapter == null) {
            if (!Framework_Registry::exists('databaseConnection')) {
                $adapter = 'Framework_Database_Driver_'.$dbAdapter;
                Framework_Registry::set('databaseConnection',new $adapter());
            }
        }
        $this->_dbAdapter = Framework_Registry::get('databaseConnection');
    }

    public function dbAdapter()
    {
        return $this->_dbAdapter;
    }
}

