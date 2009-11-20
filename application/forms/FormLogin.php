<?php

class FormLogin extends Yasui_Form
{
    public function __construct()
    {
        parent::__construct('fLogin',parse_ini_file('application/langs/spanish/Validate.ini'));
        
        $this->addElement('text', 'email', 'Correo electrónico')->addValidate('email', 'Email');
        
        $this->addElement('password', 'contrasenha', 'Contraseña');
        
        $this->addElement('submit', 'enviar', 'Conectar');
    }
}

