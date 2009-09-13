<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'library/');

require 'BootStrap.php';

Yasui_Registry::set('config', new Yasui_Config('config.ini','ini'));


$auth = Yasui_Auth::getInstance();
$authAdapter = $auth->getAdapter('DB');
$authAdapter->setAuthLocation('users')->setIdentityColumn('email')->setCredentialColumn('password')->setCredentialCrypt('md5');

$view = Yasui_View::getInstance();
$view->addHelperPath('application/views/helpers/');
$view->setPluginConf('menuAuth',array('authAdapter' => $authAdapter));
$view->authenticate = $authAdapter->isAuthenticate();

$front->dispatch();
