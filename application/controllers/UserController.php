<?php
require 'application/models/Customer.php';

class UserController extends Yasui_Controller
{
    public function loginAction()
    {
        $auth = Yasui_Auth::getInstance();
        $authAdapter = $auth->getAdapter('DB');
        
        if ($authAdapter->isAuthenticate()) {
            $this->redirect($this->_request->baseURL());
        }

        require 'application/forms/FormLogin.php';

        $formLogin = new FormLogin();

        if ($formLogin->formSent() && $formLogin->validateForm()) {
            if ($authAdapter->authenticate($formLogin->email, $formLogin->contrasenha)) {
                $this->redirect($this->_request->baseURL());
            } else {
                $this->_view->mensaje = 'Usuario o contraseña erróneos';
                $this->_view->loginForm = $formLogin;
            }
        } else {
            $this->_view->loginForm = $formLogin;
        }
    }

    public function registerAction()
    {
        $auth = Yasui_Auth::getInstance();
        $authAdapter = $auth->getAdapter('DB');

        if ($authAdapter->isAuthenticate()) {
            $this->redirect($this->_request->baseURL());
        }

        require 'application/forms/FormRegister.php';

        $formRegister = new FormRegister();
        if ($formRegister->formSent() && $formRegister->validateForm()) {
            if ($formRegister->contrasenha == $formRegister->contrasenha2) {
                $cliente = new ModelCustomer();
                if ($cliente->registrar($formRegister->nombre, $formRegister->apellidos, $formRegister->correo, $formRegister->nif, $formRegister->contrasenha)) {
                    $this->_view->mensaje = 'Gracias por registrarse, ya puede acceder';
                } else {
                    $this->_view->mensaje = $cliente->getError();
                    $this->_view->form = $formRegister;
                }
            } else {
                $this->_view->mensaje = 'Las contraseñas no coinciden';
                $this->_view->form = $formRegister;
            }
        } else {
            $this->_view->form = $formRegister;            
        }
    }

    public function logoutAction()
    {
        $auth = Yasui_Auth::getInstance();
        $authAdapter = $auth->getAdapter('DB');
        $authAdapter->deAuthenticate();
        $request = Yasui_Registry::get('request');
        $this->redirect($request->baseURL());
    }
}

