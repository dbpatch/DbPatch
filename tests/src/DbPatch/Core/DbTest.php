<?php

/**
 * Testing the Db object
 */
class DbTest extends PHPUnit_Framework_TestCase
{
    public function testDb()
    {
        $filename = 'docs/dbpatch.ini';
        $config = new DbPatch_Core_Config($filename);

        $db = new DbPatch_Core_Db($config->getConfig());

        $this->assertTrue(is_object($db));

        $this->assertTrue(is_object($db->getDb()));
        $this->assertTrue($db->getDb() instanceof Zend_Db_Adapter_Abstract);
    }
}
