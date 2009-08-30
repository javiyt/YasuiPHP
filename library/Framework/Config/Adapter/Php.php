<?php
class Framework_Config_Adapter_Php extends Framework_Config_Abstract
{
    public function __construct($file)
    {
        require CONFIG_ROOT.$file;
        $conf = basename($file,'.php');
        $this->_config = $$conf;
    }
}

