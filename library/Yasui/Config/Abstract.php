<?php
abstract class Yasui_Config_Abstract
{
    protected $_config = array();

    public function __get($key)
    {
        return $this->_config[$key];
    }
}

