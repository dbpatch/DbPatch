<?php

// Add src path is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    dirname(__FILE__) . '/../src',
    get_include_path(),
)));

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('DbPatch_');