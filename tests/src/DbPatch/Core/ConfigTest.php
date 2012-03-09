<?php

/**
 * Testing the Config object
 */
class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testIniConfig()
    {
        $filename = 'docs/dbpatch.ini';
        $config = new DbPatch_Core_Config($filename);
        $this->assertTrue(is_object($config));
        $this->assertTrue($config instanceof DbPatch_Core_Config);
        $this->assertTrue($config->getConfig() instanceof Zend_Config_Ini);
    }

    public function testPHPConfig()
    {
        $filename = 'docs/dbpatch.php';
        $config = new DbPatch_Core_Config($filename);
        $this->assertTrue(is_object($config));
        $this->assertTrue($config instanceof DbPatch_Core_Config);
        $this->assertTrue($config->getConfig() instanceof Zend_Config);
    }

    public function testXMLConfig()
    {
        $filename = 'docs/dbpatch.xml';
        $config = new DbPatch_Core_Config($filename);
        $this->assertTrue(is_object($config));
        $this->assertTrue($config instanceof DbPatch_Core_Config);
        $this->assertTrue($config->getConfig() instanceof Zend_Config_Xml);
    }
}
