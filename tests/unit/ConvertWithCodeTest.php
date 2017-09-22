<?php

use mpyw\Exceper\Convert;

class ConvertWithCodeTest extends \Codeception\TestCase\Test
{
    public function testFlow()
    {
        Convert::to('\InvalidArgumentException', 114514, function () {
            $this->assertTrue(true);
        });
        try {
            fopen();
        } catch (\PHPUnit_Framework_Exception $y) {
        }
        $this->assertTrue(isset($y));
        $this->assertEquals('fopen() expects at least 2 parameters, 0 given', $y->getMessage());
    }

    public function testFlowWithException()
    {
        try {
            Convert::to('\InvalidArgumentException', 114514, function () use (&$line) {
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

    public function testSuppressingErrors()
    {
        try {
            Convert::to('\InvalidArgumentException', 114514, function () {
                @fopen();
            });
        } catch (\InvalidArgumentException $x) {
        }
        $this->assertFalse(isset($x));
    }

    /**
     * @requires PHP 7.0.0
     */
    public function testFlowWithError()
    {
        try {
            Convert::to('\ArithmeticError', 114514, function () use (&$line) {
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
     * @expectedException \DomainException
     * @expectedExceptionMessageRegExp / must be an instance of Exception or Throwable$/
     */
    public function testStringableInstanceAsClassName()
    {
        Convert::to(new \InvalidArgumentException, 114514, function () {});
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::to() expects parameter 3 to be callable, none given
     */
    public function testMissingOneArgument()
    {
        Convert::to('dummy', 114514);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::to() expects parameter 1 to be string, array given
     */
    public function testInvalidFirstArgumentType()
    {
        Convert::to([], 114514, function () {});
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::to() expects parameter 3 to be callable, NULL given
     */
    public function testInvalidThridArgumentType()
    {
        Convert::to('dummy', 114514, null);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::to() expects parameter 4 to be integer, string given
     */
    public function testInvalidFourthArgumentType()
    {
        Convert::to('dummy', 114514, function () {}, 'dummy');
    }
}
