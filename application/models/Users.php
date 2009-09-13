<?php
class ModelCustomer extends Yasui_Database
{
    private $_table = 'users';
    private $_error = '';
    private $_info = array();

    public function getError()
    {
        return $this->_error;
    }

    public function registrar($nombre='', $apellidos='', $correo='', $contrasenha='')
    {
        if ($this->usuarioExiste($correo)) {
            $this->_error = 'Correo electrónico en uso, por favor elija otro';
            return false;
        }
        return $this->dbAdapter()->insert($this->_table,array('firstname' => $nombre, 'lastname' => $apellidos, 'email' => $correo, 'password' => md5($contrasenha), 'date_added' => 'NOW()'));
    }

    public function obtenerInformacion($id=0)
    {
        if (count($this->_info) > 0) {
            return $this->_info;
        }

        $select = array('user_id', 'firstname', 'lastname', 'email');
        if ((int)$id == 0) {
            $tmp = $this->dbAdapter()->getOne($this->_table, $select, array('email' => $id));
            if (is_array($tmp)) {
                $this->_info = $tmp;
                return $tmp;
            } else {
                return false;
            }
        } else {
            $tmp = $this->dbAdapter()->getOne($this->_table, $select, array('user_id' => $id));
            if (is_array($tmp)) {
                $this->_info = $tmp;
                return $tmp;
            } else {
                return false;
            }
        }
    }

    private function usuarioExiste($correo='')
    {
        return $this->dbAdapter()->exists($this->_table,'email',$correo);
    }
}
