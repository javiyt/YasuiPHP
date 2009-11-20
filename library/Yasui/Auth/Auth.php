<?php
class Yasui_Auth
{
    private static $instance = null;
    private $_adapter = array();

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getAdapter($adapter)
    {
        if (!in_array($adapter,array('DB'))) {
            $adapter = 'DB';
        }

        if (!isset($this->_adapter[$adapter])) {
            require 'Yasui/Auth/Adapter/' . $adapter . '.php';
            
            $class = 'Yasui_Auth_Adapter_' . $adapter;
            $this->_adapter[$adapter] = new $class;
        }

        return $this->_adapter[$adapter];
    }
}

