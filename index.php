<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'library/');

require 'BootStrap.php';

Framework_Registry::set('config', new Framework_Config('config.ini','ini'));

$auth = Framework_Auth::getInstance();
$authAdapter = $auth->getAdapter('DB');
$authAdapter->setAuthLocation('users')->setIdentityColumn('correo')->setCredentialColumn('contrasenha')->setCredentialCrypt('md5');

$front->dispatch();