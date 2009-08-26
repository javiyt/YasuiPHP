<?php

if (!defined('APPLICATION_ROOT')) {
    define('APPLICATION_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR);
}

if (!defined('CONTROLLER_ROOT')) {
    define('CONTROLLER_ROOT','controllers'.DIRECTORY_SEPARATOR);
}

if (!defined('LAYOUT_ROOT')) {
    define('LAYOUT_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'layout');
}

if (!defined('LIBRARY_ROOT')) {
    define('LIBRARY_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR);
}

if (!defined('MODELS_ROOT')) {
    define('MODELS_ROOT',APPLICATION_ROOT.'models');
}

function __autoload ($class)
{
    $clase = LIBRARY_ROOT.str_replace('_',DIRECTORY_SEPARATOR,$class).'.php';
    if (substr($class,0,5) == 'Model') {
        $clase = MODELS_ROOT.DIRECTORY_SEPARATOR.substr($class,5).'.php';
        if (file_exists($clase) && is_readable($clase)) {
            require $clase;
        }
    } else if (file_exists($clase) && is_readable($clase)) {
        require $clase;

    } else {
        $clase = LIBRARY_ROOT.str_replace('_',DIRECTORY_SEPARATOR,$class).DIRECTORY_SEPARATOR.substr($class,strrpos($class,'_') + 1).'.php';
        if (file_exists($clase) && is_readable($clase)) {
            require $clase;
        }
    }
}

Framework_Registry::set('request',new Framework_Request());
if (is_array($database)) {
    Framework_Registry::set('databaseAccess',$database);
}
$front = new Framework_Front();
$front->dispatch();