<?php

class Yasui_Lang
{
    private $_langs = array();
    private $_location = '';

    public function __construct($location='')
    {
        $this->setLocation($location);
    }

    public function setLocation($location='')
    {
        if (trim($location) != '' && is_dir($location) && is_readable($location)) {
            if (substr($location,-1) != DIRECTORY_SEPARATOR) {
                $location .= DIRECTORY_SEPARATOR;
            }
            $this->_location = $location;
        }
    }

    public function loadFile($file='')
    {
        if (!array_key_exists($file, $this->_langs) && file_exists($this->_location . $file . '.ini') && is_readable($this->_location . $file . '.ini')) {
            $this->_langs[$file] = parse_ini_file($this->_location . $file . '.ini');
        }
    }

    public function getFile($file='')
    {
        if (!array_key_exists($file, $this->_langs)) {
            return false;
        }

        return $this->_langs[$file];
    }

    public function get($key='', $file='')
    {
        if (array_key_exists($file, $this->_langs) && array_key_exists($key, $this->_langs[$file])) {
            return $this->_langs[$file][$key];
        }

        foreach($this->_langs as $files) {
            if (array_key_exists($key,$files)) {
                return $files[$key];
            }
        }

        return $key;
    }
}

