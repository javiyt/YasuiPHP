<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'library/');

require 'BootStrap.php';

Framework_Registry::set('config', new Framework_Config('config.ini','ini'));

$auth = Framework_Auth::getInstance();
$authAdapter = $auth->getAdapter('DB');
$authAdapter->setAuthLocation('users')->setIdentityColumn('user')->setCredentialColumn('password')->setCredentialCrypt('md5');

$view = Framework_View::getInstance();
$view->template()->addPath('resource','application/views/helpers/');
$view->template()->setPluginConf('menuAuth',array('authAdapter',$authAdapter));

$front->dispatch();