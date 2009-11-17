<?php

require 'Yasui/View/Helper.php';

class Yasui_View
{
    private $_template_path = array();
    private $_helper_path = array();
    private $_data = array();
    private $_layout = 'indexLayout.phtml';
    private $_baseURL;
    private $_pluginConf = array();
    static private $intance = null;
    /**
     * Matriz que almacena todas las palabras que se incluirán en el título de la aplicación separadas por el separador definido en $_separation
     * @var array
     * @access protected
     */
    protected $_titles = array();
    /**
     * Cadena que almacena el separador que se usará en el título en el template
     * @var string
     * @access protected
     */
    protected $_separation = ' :: ';
    /**
     * Matriz que almacena todas las metas que se agregarán a la cabecera en el template
     * @var array
     * @access protected
     */
    protected $_metas = array();
    /**
     * Matriz que almacena todos los archivos javascript que se incluirán en el template
     * @var array
     * @access protected
     */
    protected $_js = array();
    /**
     * Matriz que almacena todos los archivos css que se incluirán en el template
     * @var array
     * @access protected
     */
    protected $_css = array();

    private function __construct()
    {
        $this->addTemplatePath(LAYOUT_ROOT);
        $this->addHelperPath(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Helper');

        $request = Yasui_Registry::get('request');
        $this->_baseURL = $request->baseURL();
        unset($request);
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
        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        } else {
            return null;
        }
    }

    public function __toString()
    {
        return $this->fetch($this->_layout);
    }

    public function __call($name,$arguments)
    {
        $file = $name . '.php';
        $location = $this->findFile($file, 'helper');

        if ($location) {
            require_once $location;

            $class = 'Yasui_View_Helper_' . $name;
            $plugin = new $class($this->_pluginConf[$name]);

            switch (count($arguments)) {
                case 0:
                    return $plugin->$name();
                    break;
                case 1:
                    return $plugin->$name($arguments[0]);
                    break;
                case 2:
                    return $plugin->$name($arguments[0], $arguments[1]);
                    break;

                case 3:
                    return $plugin->$name($arguments[0], $arguments[1], $arguments[2]);
                    break;
                default:
                    return call_user_func_array(array($plugin, $name), $arguments);
                    break;
            }

      }
    }

    public function addTemplatePath($path='')
    {
        if (is_dir($path)) {
            if (substr($path,-1) != DIRECTORY_SEPARATOR) {
                $path .= DIRECTORY_SEPARATOR;
            }
            $this->_template_path[] = $path;
        }
    }

    public function addHelperPath($path='')
    {
        if (is_dir($path)) {
            if (substr($path, -1) != DIRECTORY_SEPARATOR) {
                $path .= DIRECTORY_SEPARATOR;
            }
            $this->_helper_path[] = $path;
        }
    }

    public function fetch($template)
    {
        $file = $this->findFile($template);
        if ($file) {
            ob_start();
            extract($this->_data, EXTR_REFS);
            require $file;

            return ob_get_clean();
        }
    }

    public function display($template)
    {
        echo $this->fetch($template);
    }

    /**
     * Añade cadenas al título de la web, para luego mostrarlas al generar la web
     * @param string $string
     * @return object
     * @access public
     */
    public function addTitle($string='')
    {
        if ($string != '' && !in_array($string, $this->_titles)) {
            $this->_titles[] = $string;
        }
        return $this;
    }

    /**
     * Configura la cadena que se usará como separador en el título de la aplicación
     * @param string $separator
     * @return object
     * @access public
     */
    public function setTitleSeparator($separator='')
    {
        if ($separator != '') {
            $this->_separation = $separator;
        }
        return $this;
    }

    /**
     * Muestra el título de la web generado a partir de la matriz que contiene todas las cadenas del título
     * @return string
     * @access public
     */
    public function headTitle()
    {
        $title = '';
        $total = count($this->_titles);
        for ($i=0;$i<$total;$i++) {
            $title .= $this->_titles[$i];
            if ($i < ($total - 1)) {
                $title .= $this->_separation;
            }
        }
        return $title;
    }

    /**
     * Agrega una etiqueta meta a la matriz de metas
     * @param string $name Nombre de la etiqueta meta
     * @param string $content Contenido de la etiqueta meta
     * @return object
     * @access public
     */
    public function addMeta($name='',$content='')
    {
        if ($name != '' && $content != '') {
            if ($name == 'keywords' && !in_array($content, $this->_metas['keywords'])) {
                $this->_metas['keywords'][] = $content;
            } else if ($name != 'keywords') {
                $this->_metas[$name] = $content;
            }
        }
        return $this;
    }

    /**
     * Devuelve todas las metas a partir de la matriz de metas
     * @return string
     * @access public
     */
    public function headMetas()
    {
        $metas = '';
        $metas .= "<meta name=\"title\" content=\"" . $this->headTitle() . "\" />";
        foreach($this->_metas as $key => $meta) {
            if ($key == 'keywords') {
                $limite = count($this->_metas['keywords']);
                if ($limite > 0) {
                    $keywords = '';
                    for($i=0;$i<$limite;$i++) {
                        $keywords .= $meta[$i];
                        if ($i < ($limite - 1)) {
                            $keywords .= ',';
                        }
                    }
                    $metas .= "<meta name=\"keywords\" content=\"$keywords\" />";
                }
            } else {
                $metas .= "<meta name=\"$key\" content=\"$meta\" />";
            }
        }
        return $metas;
    }

    /**
     * Agrega un archivo javascript a la aplicación
     * @param string $jsfile
     * @return object
     * @access public
     */
    public function addJS ($jsfile='')
    {
        $this->_js[] = $jsfile;
        return $this;
    }

    /**
     * Muestra la cadena a incluir en la web para poder incluir todos los archivos javascript requeridos
     * @return string
     * @access public
     */
    public function headJS()
    {
        $retorno = '';
        $limite = count($this->_js);
        for($i=0;$i<$limite;$i++) {
            $retorno .= "<script type=\"text/javascript\" src=\"$this->_baseURL{$this->_js[$i]}\"></script>";
        }

        return $retorno;
    }

    /**
     * Agrega un archivo CSS a la aplicación
     * @param string $cssfile
     * @return object
     * @access public
     */
    public function addCSS ($cssfile='')
    {
        $this->_css[] = $cssfile;
        return $this;
    }

    /**
     * Muestra la cadena a mostrar en la web para poder incluir todos los archivos CSS
     * @return string
     * @access public
     */
    public function headCSS()
    {
        $retorno = '';
        $limite = count($this->_css);
        for($i=0;$i<$limite;$i++) {
            $retorno .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$this->_baseURL{$this->_css[$i]}\" />";
        }
        return $retorno;
    }

    public function setPluginConf($plugin='', $configuration=array())
    {
        $this->_pluginConf[$plugin] = $configuration;
    }

    public function doctype($type='')
    {
        switch ($type) {
            case 'HTML_401_STRICT':
                return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
                break;
            case 'HTML_401_TRANSITIONAL':
                return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
                break;
            case 'HTML_401_FRAMESET':
                return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
                break;
            case 'XHTML_10_TRANSITIONAL':
                return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
                break;
            case 'XHTML_10_FRAMESET':
                return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
                break;
            case 'XHTML_11':
                return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
                break;
            default:
            case 'XHTML_10_STRICT':
                return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
                break;
        }
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
                if (file_exists($path . $file) && is_readable($path . $file)) {
                    return $path . $file;
                }
            }
        }

        return false;
    }
}