<?php
class Yasui_Config
{
    private $_adapter = null;
    private $_types = array('php', 'ini');

    public function __construct($file, $type='php')
    {
        if (!in_array($type, $this->_types)) {
            throw new Exception('Config file not recognized');
        }

        if (file_exists(CONFIG_ROOT . $file)) {
            require_once 'Yasui/Config/Adapter/' . ucfirst($type) . '.php';

            $adapter = 'Yasui_Config_Adapter_' . ucfirst($type);

            $this->_adapter = new $adapter($file);
        }
    }

    public function __get($key)
    {
        if ($this->_adapter != null) {
            return $this->_adapter->$key;
        } else {
            return false;
        }
    }

    public function toArray()
    {
        if ($this->_adapter != null) {
            return $this->_adapter->toArray();
        } else {
            return array();
        }
    }
}

