<?php

/**
 * Testing the console object
 */
class ConsoleTest extends PHPUnit_Framework_TestCase
{
    public function testGetTaskWithNoArgv()
    {
        $argv = array();
        
        $console = new DbPatch_Core_Console($arg);
        $this->assertNull($console->getTask());
    }
    
    public function testGetTaskStrippingOfTheProgname()
    {
        $argv = array('./dbpatch.php');
        
        $console = new DbPatch_Core_Console($arg);
        $this->assertNull($console->getTask());
    }
    
    public function testGetTask()
    {
        $argv = array('./dbpatch.php', 'update');
        
        $console = new DbPatch_Core_Console($argv);
        $this->assertEquals('update', $console->getTask());
    }
    
    public function testGetOptions()
    {
        $argv = array('./dbpatch.php', 'update', '--force');
        
        $console = new DbPatch_Core_Console($argv);
        $this->assertEquals(array('force' => ''), $console->getOptions());
    }
    
    public function testGetOptionsWithNoOptions()
    {
        $argv = array('./dbpatch.php', 'update');
        
        $console = new DbPatch_Core_Console($argv);
        $this->assertEquals(array(), $console->getOptions());
    }
    
    public function testGetArguments()
    {
        $argv = array('./dbpatch.php', 'update', '--skip=2,3');
        
        $console = new DbPatch_Core_Console($argv);
        $this->assertEquals(array_slice($argv, 1), $console->getArguments());
    }
}
