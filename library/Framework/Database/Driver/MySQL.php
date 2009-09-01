<?php

class Framework_Database_Driver_MySQL extends Framework_Database_Abstract
{

    /**
     * Constructor de la clase, se hace privado para forzar a usar la función estática conexión
     * @param array $datos
     * @access private
     */
    public function __construct($datos=array())
    {
        if (count($datos) == 0) {
            $config = Framework_Registry::get('config');
            $datos = $config->database;
        }

        $this->_conexion = mysql_connect($datos['server'],$datos['user'],$datos['password']);
        mysql_select_db($datos['database'],$this->_conexion);
    }

    /**
     * Desconecta de la base de datos
     */
    public function __destruct()
    {
        if ($this->_conexion) {
            mysql_close($this->_conexion);
        }
    }

    /**
     * Comprueba que un valor existe en la base de datos
     * @param string $tabla
     * @param array $campo
     * @param array $valor
     * @param array $excluir
     * @return int
     * @access public
     */
    public function exists($tabla='',$campo=array(),$valor=array(),$excluir=array())
    {
        $campos = '';
        $select = '';
        $valor = $this->_prepare($valor);
        if (!is_array($campo) && !is_array($valor)) {
            $campos = "$campo = '$valor'";
            $select = $campo;
        } else if (is_array($campo) && is_array($valor)) {
            $limite = count($campo);
            for ($i=0;$i<$limite;$i++) {
                $campos .= "{$campo[$i]} = '{$valor[$i]}'";
                $select .= $campo[$i];
                if ($i < ($limite - 1)) {
                    $campos .= ' AND ';
                    $select .= ', ';
                }
            }
        } else {
            return false;
        }

        $noincluir = '';
        if (is_array($excluir)) {
            foreach ($excluir as $key => $value) {
                if ($noincluir != '') {
                    $noincluir .= ' AND ';
                }
                $noincluir .= "$key != '$value'";
            }
            if ($noincluir != '') {
                $noincluir = 'AND '.$noincluir;
            }
        }
        $rs = mysql_query("SELECT $select FROM $tabla WHERE $campos $noincluir");
        if ($rs) {
            return mysql_num_rows($rs);
        } else {
            return false;
        }
    }

    /**
     * Devuelve un único registro de la tabla, en una matriz asociativa
     * @param string $tabla
     * @param array $campos
     * @param array $valores
     * @return array
     */
    public function getOne($tabla='',$campos=array(),$valores=array())
    {
        if (is_array($campos)) {
            $select = implode(',',$campos);
        } else {
            $select = $campos;
        }

        $valores = $this->_prepare($valores);
        $where = '';
        if (is_array($valores)) {
            foreach($valores as $key => $value) {
                if ($where != '') {
                    $where .= ' AND ';
                }
                if (preg_match('/^\W/',$value)) {
                    $value = preg_replace('/\w*/',"'$1'",$value);
                    $where .= " $key $value";
                } else {
                    $where .= " $key = '$value'";
                }

            }
            if ($where != '') {
                $where = 'WHERE '.$where;
            }
        }

        $rs = mysql_query("SELECT $select FROM $tabla $where");
        if ($rs) {
            return mysql_fetch_assoc($rs);
        } else {
            return false;
        }
    }

    /**
     * Función que obtiene todos los registros de una tabla, los campos a extraer están definidos en el parámetro campos
     * @param string $tabla
     * @param mixed $campos
     * @param array $valores
     * @param array $order
     * @return array
     */
    public function getAll($tabla='',$campos=array(),$valores=array(),$order=array())
    {
        $select = '*';
        if (is_array($campos) && count($campos) > 0) {
            $select = implode(',',$campos);
        } else if (is_string($campos) && trim($campos) != '') {
            $select = $campos;
        }

        $where = '';
        if (is_array($valores)) {
            foreach($valores as $key => $value) {
                if ($where != '') {
                    $where .= ' AND ';
                }
                $where .= "$key = '$value'";
            }
            if ($where != '') {
                $where = 'WHERE '.$where;
            }
        }

        $orderby = '';
        if (is_array($order)) {
            $orderby = implode(',',$order);
        } else if (is_string($order) && trim($order) != '') {
            $orderby = $order;
        }
        if ($orderby != '') {
            $orderby = 'ORDER BY '.$orderby;
        }
        
        $rs = mysql_query("SELECT $select FROM $tabla $where $orderby");
        if ($rs) {
            $retorno = array();
            while($row = mysql_fetch_assoc($rs)) {
                $retorno[] = $row;
            }
            mysql_free_result($rs);
            return $retorno;
        } else {
            return false;
        }
    }

    /**
     * Función que agrega un registro a la base de datos, devuelve el id del nuevo registro
     * @param string $tabla
     * @param array $valores
     * @return int
     */
    public function insert($tabla='',$valores=array())
    {
        if (!is_array($valores)) {
            return false;
        }

        $valores = $this->_prepare($valores);

        $insertar = '';
        foreach($valores as $key => $value) {
            if ($insertar != '') {
                $insertar .= ', ';
            }
            $insertar .= "$key = '$value'";
        }
        mysql_query("INSERT INTO $tabla SET $insertar");
        if (mysql_error()) {
            return false;
        } else {
            $id = mysql_insert_id();
            if ($id) {
                return $id;
            } else {
                return true;
            }
        }
    }

    /**
     * Función que actualiza los registros de una tabla
     * @param string $tabla
     * @param array $where
     * @param array $valores
     * @return int
     */
    public function update($tabla='',$where=array(),$valores=array())
    {
        $donde = '';
        if (!is_array($where)) {
            return false;
        }

        $campos = '';
        if (!is_array($valores)) {
            return false;
        }

        $where = $this->_prepare($where);
        $valores = $this->_prepare($valores);

        foreach($where as $key => $value) {
            if ($donde != '') {
                $donde .= ' AND ';
            }
            $donde .= "$key = '$value'";
        }

        foreach($valores as $key => $value) {
            if ($campos != '') {
                $campos .= ', ';
            }
            $campos .= "$key = '$value'";
        }

        mysql_query("UPDATE $tabla SET $campos WHERE $donde");

        if (mysql_error()) {
            return false;
        } else {
            return mysql_affected_rows();
        }
    }

    /**
     * Función que elimina un registro de la base de datos
     * @param string $tabla
     * @param array $valores
     * @return int
     */
    public function delete($tabla='',$valores=array())
    {
        if (!is_array($valores)) {
            return false;
        }

        $valores = $this->_prepare($valores);

        $where = '';
        foreach($valores as $key => $value) {
            if ($where != '') {
                $where .= ' AND ';
            }
            $where .= "$key = '$value'";
        }

        if ($where != '') {
            $where = 'WHERE '.$where;
        }

        mysql_query("DELETE FROM $tabla $where");
        if (mysql_error()) {
            return false;
        } else {
            return mysql_affected_rows();
        }
    }

    /**
     * Función que devuelve el número de registros que cumplen la condición proporcionada por $campos
     * @param string $tabla
     * @param array $campos
     */
    public function num($tabla='',$campos=array())
    {
        if (!is_array($campos)) {
            return false;
        }

        $valores = $this->_prepare($valores);
        $select = implode(',',array_keys($campos));

        $where = '';
        foreach($campos as $key => $value) {
            if ($where != '') {
                $where .= ' AND ';
            }
			if ($value != '') {
				$where .= "$key = '$value'";
			}
        }

        if ($where != '') {
            $where = 'WHERE '.$where;
        }

        $rs = mysql_query("SELECT $select FROM $tabla $where");
        if (mysql_error()) {
            return false;
        } else {
            return mysql_num_rows($rs);
        }
    }

    public function query($query='')
    {
        $rs = mysql_query($query);
        if (preg_match('/^SELECT(.*)/',$query)) {
            if ($rs) {
                $retorno = array();
                while($row=mysql_fetch_assoc($rs)) {
                    $retorno[] = $row;
                }
                mysql_free_result($rs);
                return $retorno;
            } else {
                return false;
            }
        }
        return $rs;
    }

    /**
     * Evitar inyección SQL, devuelve el valor de entrada escapado, permite arrays
     * @param mixed $valor
     * @return mixed
     */
    protected function _prepare($valor='')
    {
        if (is_array($valor)) {
            foreach($valor as $key => $value) {
                $valor[$key] = mysql_real_escape_string($value);
            }
            return $valor;
        } else {
            return mysql_real_escape_string($valor);
        }
    }

}

