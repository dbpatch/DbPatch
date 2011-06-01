<?php

/**
 * Testing the console object
 */
class CreateTest extends PHPUnit_Framework_TestCase
{
    public function testIssetOption()
    {
        $argv = array('./dbpatch.php', 'update', '--force');

        $console = new DbPatch_Core_Console($_SERVER['argv']);
        $this->assertTrue($console->issetOption('force'));
        $this->assertFalse($console->issetOption('bogus'));
    }
}
