<?php
require 'library/BootStrap.php';

Framework_Registry::set('config', new Framework_Config('config.ini','ini'));

$front->dispatch();