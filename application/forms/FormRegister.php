<?php
class FormRegister extends Yasui_Form
{
    public function __construct()
    {
        parent::__construct('fRegistrar',parse_ini_file('application/langs/spanish/Validate.ini'));

        $this->addElement('text','nombre','Nombre')->addValidate('nombre','Required')->addValidate('nombre','Alnum',array('whiteSpace' => true,'allAlphabets' => true));
        $this->addElement('text','apellidos','Apellidos')->addValidate('apellidos','Required')->addValidate('apellidos','Alnum',array('whiteSpace' => true,'allAlphabets' => true));

        $this->addElement('text','nif','NIF/CIF')->addValidate('nif','Required')->addValidate('nif','Between',array('min' => 8,'max' => 10));
        $this->addElement('text','correo','Correo Electrónico')->addValidate('correo','Required')->addValidate('correo','Email');

        $this->addElement('password','contrasenha','Contraseña')->addValidate('contrasenha','Required');
        $this->addElement('password','contrasenha2','Confirmar Contraseña')->addValidate('contrasenha2','Required');

        $this->addElement('submit','enviar','Registrar');
    }
}

