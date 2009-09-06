<?php
class Savant3_plugin_baseURL extends Savant3_plugin
{
    public function baseURL()
    {
        return substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], "/")+1);
    }
}

