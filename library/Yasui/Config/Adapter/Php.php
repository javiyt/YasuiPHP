<?php
require 'Yasui/Config/Abstract.php';

class Yasui_Config_Adapter_Php extends Yasui_Config_Abstract
{
    public function __construct($file)
    {
        require CONFIG_ROOT . $file;
        $conf = basename($file, '.php');
        $this->_config = $$conf;
    }
}

