<?php

class Yasui_Database_Driver_PDO extends Yasui_Database_Abstract
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
        if (isset($this->_conexion)) {
            unset($this->_conexion);
        }
    }

    protected function _connect()
    {
		if ($this->_conexion == null) {
			if (isset($this->_connectData['pdodriver'])) {
				$dsn = strtolower($this->_connectData['pdodriver']) . ':dbname=' . $this->_connectData['database'] . ';host=' . $this->_connectData['server'];
			} else {
				$dsn = 'mysql:dbname=' . $this->_connectData['database'] . ';host=' . $this->_connectData['server'];
			}
			
			$this->_conexion = new PDO($dsn, $this->_connectData['user'], $this->_connectData['password']);
		}
	}    
    
    protected function _query($query='', $valores=array())
    {
        if ($this->_conexion == null) {
            $this->_connect();
        }
		
		$sth = $this->_conexion->prepare($query);
		$sth->execute($valores);
		
		return $sth;
	}
    
    protected function _prepare($valor='')
    {
	}
    
    public function exists($tabla='', $campo=array(), $valor=array(), $excluir=array()) 
    {
        $campos = '';
        $select = '*';
        $valores = array();
        
        if (!is_array($campo) && !is_array($valor)) {
            $campos = "$campo = :$campo";
            $select = $campo;
            $valores[$campo] = $valor;
        } else if (is_array($campo) && is_array($valor)) {
            $limite = count($campo);
            for ($i=0;$i<$limite;$i++) {
                if ($this->isReserved($valor[$i])) {
                    $campos .= "{$campo[$i]} :{$campo[$i]}";
                } else {
                    $campos .= "{$campo[$i]} = :{$campo[$i]}";
                }
                $valores[$campo[$i]] = $valor[$i];
                
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
        
        $sth = $this->_query("SELECT $select FROM $tabla WHERE $campos $noincluir LIMIT 0,1", $valores);
        if (is_object($sth)) {
            return count($sth->fetchAll(PDO::FETCH_ASSOC));
        } else {
            return false;
        }			
	}
	
    public function getOne($tabla='', $campos=array(), $valores=array())
    {
		$select = '*';
		if (is_array($campos)) {
			$select = implode(', ', $campos);	
		} else if(is_string($campos) && trim($campos) != '') {
			$select = $campos;
		}
		
		$where = '';
		if (is_array($valores)) {
			foreach ($valores as $key => $value) {
                if ($where != '') {
                    $where .= ' AND ';
                }
                if ($this->isReserved($value)) {
                    $where .= " $key :$key";
                } else {
                    $where .= " $key = :$key";
                }

            }
            if ($where != '') {
                $where = 'WHERE ' . $where;
            }
		}
		
		if (is_array($valores)) {
			$sth = $this->_query("SELECT $select FROM $tabla $where LIMIT 0,1", $valores);
		} else {
			$sth = $this->_query("SELECT $select FROM $tabla $where LIMIT 0,1");
		}
		
		if (is_object($sth)) {
			return $sth->fetch(PDO::FETCH_ASSOC);
		} else {
			return false;
		}
	}
	
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
                    $where .= " $key :$key";
                } else {
                    $where .= "$key = :$key";
                }
            }
            if ($where != '') {
                $where = 'WHERE '.$where;
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
        
        $sth = $this->_query("SELECT $select FROM $tabla $where $orderby", $valores);
        if (is_object($sth)) {
            return $sth->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }		
	}
	
    public function insert($tabla='', $valores=array())
    {
		if(!is_array($valores)) {
			return false;
		}
		
		$set = '';
		foreach($valores as $key => $valor) {
			if ($set != '') {
				$set .= ', ';
			}
			$set .= $key .' = :' . $key;
		}
		
		$this->_query("INSERT INTO $tabla SET $set", $valores);
		
		return $this->_conexion->lastInsertId();
	}
	
    public function update($tabla='', $where=array(), $valores=array())
    {
		if (!is_array($where) || !is_array($valores)) {
			return false;
		}
		
		
	}
	
    public function delete($tabla='', $valores=array())
    {
	}
	
    public function num($tabla='', $campos=array())
    {
	}
	
    public function query($query='')
    {
	}
}
