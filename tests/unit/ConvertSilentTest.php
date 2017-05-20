<?php

use mpyw\Exceper\Convert;

class ConvertSilentTest extends \Codeception\TestCase\Test
{
    public function testFlowWithException()
    {
        $count = 0;
        $this->assertNull(Convert::silent(function () use (&$count) {
            ++$count;
            fopen();
            ++$count;
            fopen();
            ++$count;
        }));
        $this->assertEquals(1, $count);
        try {
            fopen();
        } catch (\PHPUnit_Framework_Exception $x) {
        }
        $this->assertTrue(isset($x));
        $this->assertEquals('fopen() expects at least 2 parameters, 0 given', $x->getMessage());
    }

    public function testSuppressingErrors()
    {
        $count = 0;
        $this->assertNull(Convert::silent(function () use (&$count) {
            ++$count;
            @fopen();
            ++$count;
            fopen();
            ++$count;
        }));
        $this->assertEquals(2, $count);
    }

    /**
     * @requires PHP 7.0.0
     */
    public function testFlowWithError()
    {
        $count = 0;
        $this->assertNull(Convert::silent(function () use (&$count) {
            ++$count;
            0 << -1;
            ++$count;
            0 << -1;
            ++$count;
        }));
        $this->assertEquals(1, $count);
        try {
            fopen();
        } catch (\PHPUnit_Framework_Exception $x) {
        }
        $this->assertTrue(isset($x));
        $this->assertEquals('fopen() expects at least 2 parameters, 0 given', $x->getMessage());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::silent() expects parameter 1 to be callable, string given
     */
    public function testInvalidFirstArgumentType()
    {
        Convert::silent('dummy');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::silent() expects parameter 2 to be integer, string given
     */
    public function testInvalidSecondArgumentType()
    {
        Convert::silent(function () {}, 'dummy');
    }
}
