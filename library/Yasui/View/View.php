<?php
class Yasui_View
{
    private $_template_path = array();
    private $_helper_path = array();
    private $_data = array();
    private $_layout = 'indexLayout.phtml';
    static private $intance = null;

    private function __construct()
    {
        $this->addTemplatePath(LAYOUT_ROOT);
    }

    static public function getInstance()
    {
        if (self::$intance == null) {
            self::$intance = new self();
        }
        return self::$intance;
    }

    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
    }

    public function __get($key)
    {
        return $this->_data[$key];
    }

    public function __toString()
    {
        return $this->fetch($this->_layout);
    }

    public function addTemplatePath($path='')
    {
        if (is_dir($path)) {
            $this->_template_path[] = $path;
        }
    }

    public function addHelperPath($path='')
    {
        if (is_dir($path)) {
            $this->_helper_path[] = $path;
        }
    }

    public function fetch($template)
    {
        $file = $this->findFile($template);
        if ($file) {
            ob_start();
            extract($this->_data,EXTR_REFS);
            require $file;

            return ob_get_clean();
        }
    }

    public function display($template)
    {
        echo $this->fetch($template);
    }

    private function findFile($file, $type='template')
    {
        if ($type == 'template') {
            $paths = $this->_template_path;
        } else if ($type == 'helper') {
            $paths = $this->_helper_path;
        }

        if (is_array($paths)) {
            foreach($paths as $path) {
                if (file_exists($path.$file) && is_readable($path.$file)) {
                    return $path.$file;
                }
            }
        }

        return false;
    }
}