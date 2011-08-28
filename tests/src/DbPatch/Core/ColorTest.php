<?php

/**
 * Testing the Color object
 */
class ColorTest extends PHPUnit_Framework_TestCase
{

    public function testErase()
    {
        $color = new DbPatch_Core_Color();

        $this->assertEquals($color->erase(), "\033[K");
    }

    public function testReset()
    {
        $color = new DbPatch_Core_Color();

        $this->assertEquals($color->reset(), "\033[K\033[0m");
        $this->assertEquals($color->reset(false), "\033[0m");
    }

    public function testColor()
    {
        $color = new DbPatch_Core_Color();
        $ret = $color->color('grey', 'bold', 'black');
        $this->assertEquals($ret, "\033[37;1;40m");

    }

    public function testValidPallet()
    {
        $color = new DbPatch_Core_Color();
        $ret = $color->pallet('error');

        $this->assertEquals($ret, "\033[37;1;41m");
    }

    public function testInValidPallet()
    {
        $color = new DbPatch_Core_Color();
        $ret = $color->pallet('error_warning');

        $this->assertEquals($ret, "\033[K\033[0m");
    }
}
