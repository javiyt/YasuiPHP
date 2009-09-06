<?php

if (!defined('APPLICATION_ROOT')) {
    define('APPLICATION_ROOT','.'.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR);
}

if (!defined('CONTROLLER_ROOT')) {
    define('CONTROLLER_ROOT','controllers'.DIRECTORY_SEPARATOR);
}

if (!defined('LAYOUT_ROOT')) {
    define('LAYOUT_ROOT','.'.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'layout'.DIRECTORY_SEPARATOR);
}

if (!defined('LIBRARY_ROOT')) {
    define('LIBRARY_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR);
}

if (!defined('MODELS_ROOT')) {
    define('MODELS_ROOT',APPLICATION_ROOT.'models'.DIRECTORY_SEPARATOR);
}

if (!defined('CONFIG_ROOT')) {
    define('CONFIG_ROOT',APPLICATION_ROOT.'configs'.DIRECTORY_SEPARATOR);
}

if (!defined('VIEWS_ROOT')) {
    define('VIEWS_ROOT',APPLICATION_ROOT.'views'.DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR);
}

if (!defined('FORMS_ROOT')) {
    define('FORMS_ROOT',APPLICATION_ROOT.'forms'.DIRECTORY_SEPARATOR);
}

if (!defined('DEFAULT_CONTROLLER')) {
    define('DEFAULT_CONTROLLER','index');
}

if (!defined('DEFAULT_ACTION')) {
    define('DEFAULT_ACTION','index');
}

require 'Yasui/Autoload.php';
require 'Yausi/Front.php';
require 'Yasui/Registry.php';
require 'Yasui/Request.php';

function __autoload ($class)
{
    $autoload = new Yasui_Autoload($class);
}

Yasui_Registry::set('request',new Yasui_Request());
if (is_array($database)) {
    Yasui_Registry::set('databaseAccess',$database);
}

$front = new Yasui_Front();
