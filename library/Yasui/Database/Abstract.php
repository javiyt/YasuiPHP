<?php

abstract class Yasui_Database_Abstract
{
    
    /**
     * Variable que almacena el recurso de conexión a la base de datos
     * @var resource
     * @access protected
     */
    protected $_conexion = null;
    protected $_reserved = array('LIKE','NOT','IS','<','>','<=','>=','=','!=');
    protected $_connectData = array();

    abstract public function exists($tabla='',$campo=array(),$valor=array(),$excluir=array());
    abstract public function getOne($tabla='',$campos=array(),$valores=array());
    abstract public function getAll($tabla='',$campos=array(),$valores=array(),$order=array());
    abstract public function insert($tabla='',$valores=array());
    abstract public function update($tabla='',$where=array(),$valores=array());
    abstract public function delete($tabla='',$valores=array());
    abstract public function num($tabla='',$campos=array());
    abstract public function query($query='');
    abstract protected function _prepare($valor='');
    abstract protected function _connect();
    abstract protected function _query($query='');

    protected function isReserved($string)
    {
        return in_array(substr($string,strpos($string,' ')),$this->_reserved);
    }
}