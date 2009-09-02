<?php

if (!defined('APPLICATION_ROOT')) {
    define('APPLICATION_ROOT','.'.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR);
}

if (!defined('CONTROLLER_ROOT')) {
    define('CONTROLLER_ROOT','controllers'.DIRECTORY_SEPARATOR);
}

if (!defined('LAYOUT_ROOT')) {
    define('LAYOUT_ROOT','.'.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'layout');
}

if (!defined('LIBRARY_ROOT')) {
    define('LIBRARY_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR);
}

if (!defined('MODELS_ROOT')) {
    define('MODELS_ROOT',APPLICATION_ROOT.'models');
}

if (!defined('CONFIG_ROOT')) {
    define('CONFIG_ROOT',APPLICATION_ROOT.'configs'.DIRECTORY_SEPARATOR);
}

if (!defined('DEFAULT_CONTROLLER')) {
    define('DEFAULT_CONTROLLER','index');
}

if (!defined('DEFAULT_ACTION')) {
    define('DEFAULT_ACTION','index');
}

require 'Framework/Autoload.php';
require 'Framework/Front.php';
require 'Framework/Registry.php';
require 'Framework/Request.php';

function __autoload ($class)
{
    $autoload = new Framework_Autoload($class);
}

Framework_Registry::set('request',new Framework_Request());
if (is_array($database)) {
    Framework_Registry::set('databaseAccess',$database);
}

$front = new Framework_Front();
