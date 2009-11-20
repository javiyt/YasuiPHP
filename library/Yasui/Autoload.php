<?php
/**
 * Yasui Framework
 *
 * @category   Yasui
 * @package    Yasui_Autoload
 * @license    GNU Lesser General Public License (LGPL) version 2.1
 */

class Yasui_Autoload
{
    public function __construct($class)
    {
        //The file to find based on class name
        $clase = LIBRARY_ROOT . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        //If it is a model the file to find
        if (substr($class, 0, 5) == 'Model') {
            //The file to find it is into models directory, and its name is the name of the model without the prefix Model
            $clase = MODELS_ROOT . DIRECTORY_SEPARATOR . substr($class, 5) . '.php';
            if (file_exists($clase) && is_readable($clase)) {
                require $clase;
            }
        } else if (file_exists($clase) && is_readable($clase)) {
            //It is not a model what has to find
            require $clase;

        } else {
            //New name of file to find, under a directory into library folder
            $clase = LIBRARY_ROOT . str_replace('_', DIRECTORY_SEPARATOR, $class) . DIRECTORY_SEPARATOR . substr($class, strrpos($class, '_') + 1) . '.php';
            if (file_exists($clase) && is_readable($clase)) {
                require $clase;
            }
        }
        //Nothing to return
        return;
    }
}

