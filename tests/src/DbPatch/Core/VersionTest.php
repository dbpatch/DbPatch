<?php

/**
 * Testing the Version object
 */
class VersionTest extends PHPUnit_Framework_TestCase
{
    public function testCheckExact()
    {
        $version = "0.99.9";
        $this->assertFalse(DbPatch_Core_Version::checkExact($version));

        $version = DbPatch_Core_Version::VERSION;
        $this->assertTrue(DbPatch_Core_Version::checkExact($version));
    }

    public function testCheckMinimal()
    {
        $version = '0.0.0';
        $this->assertEquals(true, DbPatch_Core_Version::checkMinimal($version));

        $version = DbPatch_Core_Version::VERSION;
        $this->assertEquals(true, DbPatch_Core_Version::checkMinimal($version));

        $version = '99.0.0';
        $this->assertEquals(false, DbPatch_Core_Version::checkMinimal($version));
    }
}
