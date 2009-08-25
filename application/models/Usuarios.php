<?php
final class ModelUsuarios extends Framework_Database_MySQL
{

    public function getMunicipios()
    {
        return $this->getAll('users', '*');
    }
}
