<?php
class Framework_Config_Adapter_Ini extends Framework_Config_Abstract
{
    public function __construct($file)
    {
        $this->_config = parse_ini_file(CONFIG_ROOT.$file,true);
    }
}

