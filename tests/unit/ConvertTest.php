<?php

use mpyw\Exceper\Convert;

class ConvertTest extends \Codeception\TestCase\Test
{
    public function testFlow()
    {
        Convert::to('\InvalidArgumentException', function () {
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
            Convert::to('\InvalidArgumentException', function () use (&$line) {
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

    public function testSuppressingErrors()
    {
        try {
            Convert::to('\InvalidArgumentException', function () {
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
            Convert::to('\ArithmeticError', function () use (&$line) {
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
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Call to undefined method mpyw\Exceper\Convert::dummy()
     */
    public function testInvalidMethodName()
    {
        Convert::dummy();
    }

    /**
     * @expectedException \DomainException
     * @expectedExceptionMessageRegExp / must be an instance of Exception or Throwable$/
     */
    public function testStringableInstanceAsClassName()
    {
        Convert::to(new \InvalidArgumentException, function () {});
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::to() expects at least 2 parameters, 0 given
     */
    public function testMissingTwoArguments()
    {
        Convert::to();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::to() expects at least 2 parameters, 1 given
     */
    public function testMissingOneArgument()
    {
        Convert::to('dummy');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::to() expects parameter 1 to be string, array given
     */
    public function testInvalidFirstArgumentType()
    {
        Convert::to([], function () {});
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::to() expects parameter 2 to be callable, string given
     */
    public function testInvalidSecondArgumentType()
    {
        Convert::to('dummy', 'dummy');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage mpyw\Exceper\Convert::to() expects parameter 3 to be integer, string given
     */
    public function testInvalidThirdArgumentType()
    {
        Convert::to('dummy', function () {}, 'dummy');
    }
}
