<?php
class Framework_Session
{
    protected static $instance = null;
    private $_namespace;
    private $_adapter;

    protected function __construct($namespace='',$adapter='session')
    {
        if ($adapter == 'database') {
            $this->_adapter = new Framework_Session_Adapter_DB();
            session_set_save_handler(array($this->_adapter,'open'),array($this->_adapter,'close'),array($this->_adapter,'read'),array($this->_adapter,'write'),array($this->_adapter,'destroy'),array($this->_adapter,'gc'));
        } else {
            $this->_adapter = $adapter;
        }
        session_start();
        $this->_namespace = $namespace;
    }

    public static function start($namespace='',$adapter='session')
    {
        if (self::$instance == null) {
            self::$instance = new self($namespace,$adapter);
        }

        return self::$instance;
    }

    public function setConfig($config=array())
    {
        if ($this->_adapter instanceof Framework_Session_Adapter_DB) {
            $this->_adapter->setConfig($config);
        }

        return $this;
    }

    public function __set($key,$value)
    {
        $_SESSION[$this->_namespace][$key] = $value;
    }

    public function __get($key)
    {
        if (isset($_SESSION[$this->_namespace][$key])) {
            return $_SESSION[$this->_namespace][$key];
        } else {
            return false;
        }
    }

    public function __isset($key)
    {
        return isset($_SESSION[$this->_namespace][$key]);
    }

    public function __unset($key)
    {
        unset($_SESSION[$this->_namespace][$key]);
        return true;
    }
}

