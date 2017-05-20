<?php

use mpyw\Exceper\Convert;

class ConvertToErrorExceptionTest extends \Codeception\TestCase\Test
{
    public function testFlow()
    {
        Convert::toErrorException(function () {
            $this->assertTrue(true);
        });
        try {
            fopen();
        } catch (\PHPUnit_Framework_Exception $x) {
        }
        $this->assertTrue(isset($x));
        $this->assertEquals('fopen() expects at least 2 parameters, 0 given', $x->getMessage());
    }

    public function testFlowWithException()
    {
        try {
            Convert::toErrorException(function () use (&$line) {
                $line = __LINE__; fopen();
            });
        } catch (\ErrorException $x) {
        }
        $this->assertTrue(isset($x));
        $this->assertEquals(0, $x->getCode());
        $this->assertEquals(__FILE__, $x->getFile());
        $this->assertEquals($line, $x->getLine());
        $this->assertEquals('fopen() expects at least 2 parameters, 0 given', $x->getMessage());
        try {
            fopen();
        } catch (\PHPUnit_Framework_Exception $y) {
        }
        $this->assertTrue(isset($y));
        $this->assertEquals('fopen() expects at least 2 parameters, 0 given', $y->getMessage());
    }

    public function testSuppressingErrors()
    {
        try {
            Convert::toErrorException(function () {
                @fopen();
            });
        } catch (\InvalidArgumentException $x) {
        }
        $this->assertFalse(isset($x));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::toErrorException() expects parameter 1 to be callable, string given
     */
    public function testInvalidFirstArgumentType()
    {
        Convert::toErrorException('dummy');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::toErrorException() expects parameter 2 to be integer, string given
     */
    public function testInvalidSecondArgumentType()
    {
        Convert::toErrorException(function () {}, 'dummy');
    }
}
