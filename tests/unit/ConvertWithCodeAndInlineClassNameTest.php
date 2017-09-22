<?php

use mpyw\Exceper\Convert;

class ConvertWithCodeAndInlineClassNameTest extends \Codeception\TestCase\Test
{
    public function testFlow()
    {
        Convert::toInvalidArgumentException(114514, function () {
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
            Convert::toInvalidArgumentException(114514, function () use (&$line) {
                $line = __LINE__; fopen();
            });
        } catch (\InvalidArgumentException $x) {
        }
        $this->assertTrue(isset($x));
        $this->assertEquals(114514, $x->getCode());
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
     * @requires PHP 7.0.0
     */
    public function testFlowWithError()
    {
        try {
            Convert::toArithmeticError(114514, function () use (&$line) {
                $line = __LINE__; fopen();
            });
        } catch (\ArithmeticError $x) {
        }
        $this->assertTrue(isset($x));
        $this->assertEquals(114514, $x->getCode());
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
     * @expectedExceptionMessage mpyw\Exceper\Convert::toInvalidArgumentException() expects parameter 2 to be callable, none given
     */
    public function testMissingOneArgument()
    {
        Convert::toInvalidArgumentException(114514);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::toInvalidArgumentException() expects parameter 2 to be callable, NULL given
     */
    public function testInvalidSecondArgumentType()
    {
        Convert::toInvalidArgumentException(114514, null);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::toInvalidArgumentException() expects parameter 3 to be integer, string given
     */
    public function testInvalidThirdArgumentType()
    {
        Convert::toInvalidArgumentException(114514, function () {}, 'dummy');
    }
}
