<?php
/**
 * @package CMS
 * @version 0.1
 * Clase que amplia las posibilidades que nos ofrece Savant para gestionar los templates
 */

//Incluye la clase padre, es decir Savant
require 'Yasui/View/Savant3.php';

class Template extends Savant3
{
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
    /**
     * Cadena que almacena la url base de la aplicación, por si se usa mod_rewrite incluir los archivos correctamente
     * @var string
     * @access protected
     */
    protected $baseURL;

    /**
     * Constructor de la clase, recibe la configuración básica del template como parámetro
     * @param array $config
     * @access public
     */
    public function __construct($config=array())
    {
        parent::__construct($config);
        $this->_metas['keywords'] = array();
        $this->baseURL = $config['baseURL'];
        $this->addPath('resource', dirname(__FILE__).'/Helpers/');
    }

    /**
     * Añade cadenas al título de la web, para luego mostrarlas al generar la web
     * @param string $string
     * @return object
     * @access public
     */
    public function addTitle($string='')
    {
        if ($string != '' && !in_array($string,$this->_titles)) {
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
            if ($name == 'keywords' && !in_array($content,$this->_metas['keywords'])) {
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
        $metas .= "<meta name=\"title\" content=\"".$this->headTitle()."\" />";
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
        $local = '<script type="text/javascript" src="cssjs.php?files=';
        $limite = count($this->_js);
        for($i=0;$i<$limite;$i++) {
            if (substr($this->_js[$i],0,4) != 'http') {
                if (file_exists('CMS_includes/'.$this->_js[$i])) {
                    $local .= $this->_js[$i].',';
                } else {
                    $retorno .= "<script type=\"text/javascript\" src=\"$this->baseURL{$this->_js[$i]}\"></script>";
                }
            } else {
                $retorno .= "<script type=\"text/javascript\" src=\"{$this->_js[$i]}\"></script>";
            }
        }
        $local .= '"></script>';

        return $retorno.$local;
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
            $retorno .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$this->baseURL{$this->_css[$i]}\" />";
        }
        return $retorno;
    }

    /**
     * Procesa un archivo como parcial para mostrarlo en una parte del template
     * @param string $tpl
     * @return string
     * @access public
     */
    public function partial($tpl='')
    {
        return $this->fetch($tpl);
    }

    /**
     * Agrega una nueva carpeta para procesar templates
     * @param string $dir
     * @access public
     */
    public function addTemplatePath($dir='')
    {
        $this->addPath('template',$dir);
        return $this;
    }

    public function displayIndex()
    {
        echo $this->getIndex();
    }

    public function getIndex()
    {
        if ($this->findFile('template', 'index.phtml')) {
            $contenido = $this->fetch('index.phtml');
        } else if ($this->findFile('template', 'index.html')) {
            $contenido = $this->fetch('index.html');
        } else {
            return false;
        }

        if (count($this->_titles)) {
            $title = $this->headTitle();
            $contenido = preg_replace('/<title>(.*)<\/title>/',"<title>$title</title>",$contenido);
        }

        if (count($this->_metas)) {
            $metas = $this->headMetas();
            $contenido = preg_replace('/<meta(.*)(keywords|description)(.*)\/>/','',$contenido);
            $contenido = preg_replace('/<title>(.*)<\/title>/',"<title>$1</title>$metas",$contenido);
        }

        return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $contenido);
    }
}
