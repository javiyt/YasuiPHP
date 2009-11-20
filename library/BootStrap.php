<?php
/**
 * Yasui Framework
 *
 * @category   Yasui
 * @package    Yasui_Bootstrap
 * @license    GNU Lesser General Public License (LGPL) version 2.1
 */

 //If it is not defined application root directory, it is defined the default recommended application root directory
if (!defined('APPLICATION_ROOT')) {
    define('APPLICATION_ROOT', '.' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);
}

 //If it is not defined controller root directory, it is defined the default recommended controller root directory
 //Controller directory exists under application root directory
if (!defined('CONTROLLER_ROOT')) {
    define('CONTROLLER_ROOT', 'controllers' . DIRECTORY_SEPARATOR);
}

//If it is not defined layout root directory, it is defined the default recommended layout root directory
if (!defined('LAYOUT_ROOT')) {
    define('LAYOUT_ROOT', '.' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR);
}

//If it is not defined library root directory, it is defined the default recommended library root directory
if (!defined('LIBRARY_ROOT')) {
    define('LIBRARY_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
}

//If it is not defined models root directory, it is defined the default recommended models root directory
if (!defined('MODELS_ROOT')) {
    define('MODELS_ROOT', APPLICATION_ROOT . 'models' . DIRECTORY_SEPARATOR);
}

//If it is not defined configs root directory, it is defined the default recommended configs root directory
if (!defined('CONFIG_ROOT')) {
    define('CONFIG_ROOT', APPLICATION_ROOT . 'configs' . DIRECTORY_SEPARATOR);
}

//If it is not defined view scripts root directory, it is defined the default recommended view scripts root directory
if (!defined('VIEWS_ROOT')) {
    define('VIEWS_ROOT', APPLICATION_ROOT . 'views' . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR);
}

//If it is not defined forms root directory, it is defined the default recommended forms root directory
if (!defined('FORMS_ROOT')) {
    define('FORMS_ROOT', APPLICATION_ROOT . 'forms' . DIRECTORY_SEPARATOR);
}

//Define the default controller, if it is not defined before
if (!defined('DEFAULT_CONTROLLER')) {
    define('DEFAULT_CONTROLLER', 'index');
}

//Define the default action, if it is not defined before
if (!defined('DEFAULT_ACTION')) {
    define('DEFAULT_ACTION', 'index');
}

if (!defined('VIEWS_EXTENSION')) {
    define('VIEWS_EXTENSION', '.phtml');
}

//Require neccesary files to execute the framework
require 'Yasui/Autoload.php';
require 'Yasui/Front.php';
require 'Yasui/Registry.php';
require 'Yasui/Request.php';
require 'Yasui/Router.php';
require 'Yasui/View/View.php';

//Define the autoload function, if a required script is not loaded, the autoload function try to load it
function __autoload($class)
{
    //Instanstiate autoload class
    $autoload = new Yasui_Autoload($class);
}

//Create the request class and save it into the register
Yasui_Registry::set('request', new Yasui_Request());
//Create the router class and save it into the register
Yasui_Registry::set('router', new Yasui_Router());

//Create the front controller
$front = new Yasui_Front();