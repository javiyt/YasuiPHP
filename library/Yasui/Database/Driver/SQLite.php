<?php
require 'Yasui/Database/Abstract.php';

class Yasui_Database_Driver_SQLite extends Yasui_Database_Abstract
{
    /**
     * Constructor de la clase, se hace privado para forzar a usar la función estática conexión
     * @param array $datos
     * @access private
     */
    public function __construct($datos=array())
    {
        if (count($datos) == 0) {
            $config = Yasui_Registry::get('config');
            $this->_connectData = $config->database;
        } else {
            $this->_connectData = $datos;
        }
    }

    /**
     * Desconecta de la base de datos
     */
    public function __destruct()
    {
        if ($this->_conexion) {
            sqlite_close($this->_conexion);
        }
    }

    protected function _connect()
    {
        if ($this->_conexion == null) {
            $this->_conexion = new SQLiteDatabase($this->_connectData['filename'], $this->_connectData['mode']);
        }
    }

    protected function _query($query='')
    {
        if ($this->_conexion == null) {
            $this->_connect();
        }

        return sqlite_query($query, $this->_conexion, SQLITE_ASSOC);
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
    public function exists($tabla='', $campo=array(), $valor=array(), $excluir=array())
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
                if ($this->isReserved($valor[$i])) {
                    $campos .= "{$campo[$i]} {$valor[$i]}";
                } else {
                    $campos .= "{$campo[$i]} = '{$valor[$i]}'";
                }

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
                $noincluir = 'AND ' . $noincluir;
            }
        }
        $rs = $this->_query("SELECT $select FROM $tabla WHERE $campos $noincluir");
        if ($rs) {
            return sqlite_num_rows($rs);
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
    public function getOne($tabla='', $campos=array(), $valores=array())
    {
        if (is_array($campos)) {
            $select = implode(',', $campos);
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
                if ($this->isReserved($value)) {
                    $where .= " $key $value";
                } else {
                    $where .= " $key = '$value'";
                }

            }
            if ($where != '') {
                $where = 'WHERE ' . $where;
            }
        }

        $rs = $this->_query("SELECT $select FROM $tabla $where");
        if ($rs) {
            return mysql_fetch_array($rs);
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
    public function getAll($tabla='', $campos=array(), $valores=array(), $order=array())
    {
        $select = '*';
        if (is_array($campos) && count($campos) > 0) {
            $select = implode(',', $campos);
        } else if (is_string($campos) && trim($campos) != '') {
            $select = $campos;
        }

        $where = '';
        if (is_array($valores)) {
            foreach($valores as $key => $value) {
                if ($where != '') {
                    $where .= ' AND ';
                }
                if ($this->isReserved($value)) {
                    $where .= " $key $value";
                } else {
                    $where .= "$key = '$value'";
                }
            }
            if ($where != '') {
                $where = 'WHERE ' . $where;
            }
        }

        $orderby = '';
        if (is_array($order)) {
            $orderby = implode(',', $order);
        } else if (is_string($order) && trim($order) != '') {
            $orderby = $order;
        }
        if ($orderby != '') {
            $orderby = 'ORDER BY ' . $orderby;
        }

        $rs = $this->_query("SELECT $select FROM $tabla $where $orderby");
        if ($rs) {
            $retorno = array();
            while($row = sqlite_fetch_array($rs)) {
                $retorno[] = $row;
            }
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
    public function insert($tabla='', $valores=array())
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
        $this->_query("INSERT INTO $tabla SET $insertar");
        if (sqlite_last_error()) {
            return false;
        } else {
            $id = sqlite_last_insert_rowid($this->_conexion);
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
    public function update($tabla='', $where=array(), $valores=array())
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

        $this->_query("UPDATE $tabla SET $campos WHERE $donde");

        if (sqlite_last_error()) {
            return false;
        } else {
            return sqlite_changes();
        }
    }

    /**
     * Función que elimina un registro de la base de datos
     * @param string $tabla
     * @param array $valores
     * @return int
     */
    public function delete($tabla='', $valores=array())
    {
        $where = '';
        $valores = $this->_prepare($valores);

        if (is_array($valores)) {
            foreach($valores as $key => $value) {
                if ($where != '') {
                    $where .= ' AND ';
                }
                if ($this->isReserved($value)) {
                    $where .= "$key $value";
                } else {
                    $where .= "$key = '$value'";
                }
            }
        } else if (trim($valores) != '') {
            $where = $valores;
        }

        if ($where != '') {
            $where = 'WHERE ' . $where;
        }

        $this->_query("DELETE FROM $tabla $where");
        if (sqlite_last_error()) {
            return false;
        } else {
            return sqlite_changes();
        }
    }

    /**
     * Función que devuelve el número de registros que cumplen la condición proporcionada por $campos
     * @param string $tabla
     * @param array $campos
     */
    public function num($tabla='', $campos=array())
    {
        if (!is_array($campos)) {
            return false;
        }

        $valores = $this->_prepare($valores);
        $select = implode(',', array_keys($campos));

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
            $where = 'WHERE ' . $where;
        }

        $rs = $this->_query("SELECT $select FROM $tabla $where");
        if (sqlite_last_error()) {
            return false;
        } else {
            return sqlite_num_rows($rs);
        }
    }

    public function query($query='')
    {
        $rs = $this->_query($query);
        if (preg_match('/^SELECT/', $query)) {
            if ($rs) {
                $retorno = array();
                while($row=sqlite_fech_array($rs)) {
                    $retorno[] = $row;
                }
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
        $this->_connect();
        if (is_array($valor)) {
            foreach($valor as $key => $value) {
                $valor[$key] = sqlite_escape_string($value);
            }
            return $valor;
        } else {
            return sqlite_escape_string($valor);
        }
    }
    
}