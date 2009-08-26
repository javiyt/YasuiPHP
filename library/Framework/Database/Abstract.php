<?php

abstract class Framework_Database_Abstract
{
    
    /**
     * Variable que almacena el recurso de conexión a la base de datos
     * @var resource
     * @access protected
     */
    protected $conexion = null;


    abstract public function exists($tabla='',$campo=array(),$valor=array(),$excluir=array());
    abstract public function getOne($tabla='',$campos=array(),$valores=array());
    abstract public function getAll($tabla='',$campos=array(),$valores=array(),$order=array());
    abstract public function insert($tabla='',$valores=array());
    abstract public function update($tabla='',$where=array(),$valores=array());
    abstract public function delete($tabla='',$valores=array());
    abstract public function num($tabla='',$campos=array());
    abstract public function query($query='');
    abstract protected function prepare($valor='');
}