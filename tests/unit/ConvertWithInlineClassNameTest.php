<?php

use mpyw\Exceper\Convert;

class ConvertWithInlineClassNameTest extends \Codeception\TestCase\Test
{
    public function testFlow()
    {
        Convert::toInvalidArgumentException(function () {
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
            Convert::toInvalidArgumentException(function () use (&$line) {
                $line = __LINE__; fopen();
            });
        } catch (\InvalidArgumentException $x) {
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

    /**
     * @requires PHP >= 7
     */
    public function testFlowWithError()
    {
        try {
            Convert::toArithmeticError(function () use (&$line) {
                $line = __LINE__; fopen();
            });
        } catch (\ArithmeticError $x) {
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

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::toInvalidArgumentException() expects at least 1 parameters, 0 given
     */
    public function testMissingOneArgument()
    {
        Convert::toInvalidArgumentException();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::toInvalidArgumentException() expects parameter 1 to be callable, string given
     */
    public function testInvalidFirstArgumentType()
    {
        Convert::toInvalidArgumentException('dummy');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::toInvalidArgumentException() expects parameter 2 to be integer, string given
     */
    public function testInvalidSecondArgumentType()
    {
        Convert::toInvalidArgumentException(function () {}, 'dummy');
    }
}
