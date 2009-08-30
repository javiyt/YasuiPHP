<?php
final class ModelUsuarios extends Framework_Database
{

    public function getMunicipios()
    {
        return $this->dbAdapter()->getAll('users', '*');
    }
}
