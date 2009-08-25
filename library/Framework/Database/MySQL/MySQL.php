<?php
/**
 * Driver de acceso a mysql
 *
 * @package CMS
 * @version 0.1
 */

//require dirname(__FILE__).'/../Database.php';

class Framework_Database_MySQL extends Framework_Database {

    /**
     * Constructor de la clase, se hace privado para forzar a usar la función estática conexión
     * @param array $datos
     * @access private
     */
    public function __construct($datos=array())
    {
        if (count($datos) == 0) {
            $datos = Framework_Registry::get('databaseAccess');
        }

        if (!Framework_Registry::exists('databaseConnection')) {
            $this->conexion = mysql_connect($datos['server'],$datos['user'],$datos['password']);
            mysql_select_db($datos['database'],$this->conexion);
            Framework_Registry::set('databaseConnection',$this->conexion);
        } else {
            $this->conexion = Framework_Registry::get('databaseConnection');
        }
    }

    /**
     * Desconecta de la base de datos
     */
    public function __destruct()
    {
        if ($this->conexion) {
            mysql_close($this->conexion);
        }
    }

    /**
     * Realiza la conexión a la base de datos mediante los datos proporcionados, en caso de estar ya conectado devuelve el objeto de conexión, patrón Singleton
     * @param array $datos
     * @return object
     * @access public
     */
    public static function conectar($datos=array())
    {
        if (count($datos) == 0) {
            $datos = Framework_Registry::get('databaseAccess');
        }
        if (self::$instancia == null) {
            self::$instancia = new self($datos);
        }
        return self::$instancia;
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
        $valor = $this->prepare($valor);
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
        //$this->firephp->info("SELECT $select FROM $tabla WHERE $campos $noincluir");
        //echo "SELECT $select FROM $tabla WHERE $campos $noincluir";
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

        $valores = $this->prepare($valores);
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
        //echo "SELECT $select FROM $tabla $where";
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

        $valores = $this->prepare($valores);

        $insertar = '';
        foreach($valores as $key => $value) {
            if ($insertar != '') {
                $insertar .= ', ';
            }
            $insertar .= "$key = '$value'";
        }
        //echo "INSERT INTO $tabla SET $insertar";
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

        $where = $this->prepare($where);
        $valores = $this->prepare($valores);

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

        //echo "UPDATE $tabla SET $campos WHERE $donde";
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

        $valores = $this->prepare($valores);

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

        $valores = $this->prepare($valores);
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
    protected function prepare($valor='')
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
?>
