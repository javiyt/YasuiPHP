<?php
class Yasui_Autoload
{
    public function __construct($class)
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
        return;
    }
}

