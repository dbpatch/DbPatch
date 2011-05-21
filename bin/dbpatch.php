#!/usr/bin/env php
<?php
 /**
  * DbPatch
  *
  * @category   DbPatch
  * @package    CLI
  * @copyright  Copyright (c) 2010-2011
  */

 // determine base include folder, if @php_bin@ contains @php_bin then we do not install via PEAR
 $base_include_folder = (strpos('@php_dir@', '@php_dir') === 0)
   ? dirname(__FILE__) . '/../src'
   : '@php_dir@/DbPatch/src';

 // set path to add lib folder, load the Zend Autoloader and include the symfony timer
 set_include_path(realpath($base_include_folder) . PATH_SEPARATOR . get_include_path());

 require_once 'Zend/Loader/Autoloader.php';
 $autoloader = Zend_Loader_Autoloader::getInstance();
 $autoloader->registerNamespace('DbPatch_');

 $application = new DbPatch_Core_Application();
 $application->main();

 // disable E_STRICT reporting on the end to prevent PEAR from throwing Strict warnings.
 error_reporting(error_reporting() & ~E_STRICT);
