<?php
class Framework_Database
{
    public function __construct($dbAdapter='MySQL')
    {
        if (!Framework_Registry::exists('databaseConnection')) {
            $adapter = 'Framework_Database_'.$dbAdapter;
            Framework_Registry::set('databaseConnection',new $adapter());
        }
        return Framework_Registry::get('databaseConnection');
    }
}

