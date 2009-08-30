<?php
class Framework_Config
{
    private $_adapter;
    private $_types = array('php','ini');

    public function __construct($file,$type='php')
    {
        if (!in_array($type,$this->_types)) {
            throw new Exception('Config file not recognized');
        }

        if (file_exists(CONFIG_ROOT.$file)) {
            $adapter = 'Framework_Config_Adapter_'.ucfirst($type);
            $this->_adapter = new $adapter($file);
        }
    }

    public function __get($key)
    {
        return $this->_adapter->$key;
    }
}

