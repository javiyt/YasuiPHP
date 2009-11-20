<?php
require 'Yasui/Config/Abstract.php';

class Yasui_Config_Adapter_Ini extends Yasui_Config_Abstract
{
    public function __construct($file)
    {
        $this->_config = parse_ini_file(CONFIG_ROOT . $file, true);
    }
}

