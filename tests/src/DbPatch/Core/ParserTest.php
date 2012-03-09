<?php

/**
 * Testing the dbpatch parser
 */
class ParserTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultReplacements()
    {
        $command = "mysql -h {host} -p {dbname}";
        $params = array(
            'host'   => 'localhost',
            'dbname' => 'mydatabse'
        );

        $this->assertEquals('mysql -h localhost -p mydatabse', DbPatch_Core_Parser::parse($command, $params));
    }

    public function testMissingParameter()
    {
        $command = "mysql -h {host} -p{password} {dbname}";
        $params = array(
            'host'   => 'localhost',
            'dbname' => 'mydatabse'
        );

        $this->assertEquals('mysql -h localhost -p{password} mydatabse', DbPatch_Core_Parser::parse($command, $params));
    }

    /**
     * @expectedException DbPatch_Exception
     */
    public function testInvalidParams()
    {
        $command = "mysql -h {host} -p{password} {dbname}";
        $this->assertEquals('mysql -h localhost -p{password} mydatabse', DbPatch_Core_Parser::parse($command, null));
    }

    public function testCleaningEmptyParams()
    {
        $command = "mysql -h {host} {%password%}-p{password} {%password%}{dbname}";
        $params = array(
            'host'   => 'localhost',
            'dbname' => 'mydatabse'
        );

        $this->assertEquals('mysql -h localhost mydatabse', DbPatch_Core_Parser::parse($command, $params));
    }

    public function testCleaningEmptyParamsWithEmptyValue()
    {
        $command = "mysql -h {host} {%password%}-p{password} {%password%}{dbname}";
        $params = array(
            'host'   => 'localhost',
            'dbname' => 'mydatabse',
            'password' => ''
        );

        $this->assertEquals('mysql -h localhost mydatabse', DbPatch_Core_Parser::parse($command, $params));
    }

    public function testCleaningEmptyParamsWithAllValuesSet()
    {
        $command = "mysql -h {host} {%password%}-p{password} {%password%}{dbname}";
        $params = array(
            'host'   => 'localhost',
            'dbname' => 'mydatabse',
            'password' => 'secret'
        );

        $this->assertEquals('mysql -h localhost -psecret mydatabse', DbPatch_Core_Parser::parse($command, $params));
    }

    public function testCleaningEmptyParamsWithNoClosingTag()
    {
        $invalid = "mysql -h {host} {%password%}-p{password} {dbname}";
        $params = array(
            'host'   => 'localhost',
            'dbname' => 'mydatabse',
            'password' => ''
        );

        $this->assertEquals('mysql -h localhost {%password%}-p mydatabse', DbPatch_Core_Parser::parse($invalid, $params));
    }

    public function testCompleteCommand()
    {
        $command = "mysql -h{host} {%port%}-P{port} {%port%}-u{username} {%password%}-p{password} {%password%}--default-character-set={charset} {dbname} < {filename} 2>&1";
        $params = array(
            'host'     => 'localhost',
            'username' => 'joe',
            'password' => 'secret',
            'charset'  => 'utf-8',
            'dbname'   => 'mydatabse',
            'filename' => 'patch-0001.sql'
        );

        $this->assertEquals('mysql -hlocalhost -ujoe -psecret --default-character-set=utf-8 mydatabse < patch-0001.sql 2>&1', DbPatch_Core_Parser::parse($command, $params));
    }

}
