<?php

namespace Mpyw\Exceper\Tests;

use Mpyw\Exceper\Convert;

class ConvertTest extends TestCase
{
    public function testFlow()
    {
        $that = $this;
        Convert::to('\InvalidArgumentException', function () use ($that) {
            $that->assertTrue(true);
        });

        $this->shouldTriggerFopenWarning();
        $this->shouldTriggerExceptionWithMessage($this->fopenErrorMessage());
        fopen();
    }

    public function testFlowWithException()
    {
        if (\version_compare(PHP_VERSION, '8', '>=')) {
            $this->shouldTriggerFopenWarning();
            $this->shouldTriggerFopenWarningMessage($this->fopenErrorMessage());
        }

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
        $this->assertEquals($this->fopenErrorMessage(), $x->getMessage());

        $this->shouldTriggerFopenWarning();
        $this->shouldTriggerExceptionWithMessage($this->fopenErrorMessage());
        fopen();
    }

    public function testSuppressingErrors()
    {
        if (\version_compare(PHP_VERSION, '8', '>=')) {
            $this->shouldTriggerFopenWarning();
            $this->shouldTriggerFopenWarningMessage($this->fopenErrorMessage());
        }

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
        if (\version_compare(PHP_VERSION, '8', '>=')) {
            $this->shouldTriggerFopenWarning();
            $this->shouldTriggerFopenWarningMessage($this->fopenErrorMessage());
        }

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
        $this->assertEquals($this->fopenErrorMessage(), $x->getMessage());

        $this->shouldTriggerFopenWarning();
        $this->shouldTriggerExceptionWithMessage($this->fopenErrorMessage());
        fopen();
    }

    public function testInvalidMethodName()
    {
        $this->shouldTriggerException('\BadMethodCallException');
        $this->shouldTriggerExceptionWithMessage('Call to undefined method Mpyw\Exceper\Convert::dummy()');

        Convert::dummy();
    }

    public function testStringableInstanceAsClassName()
    {
        $this->shouldTriggerException('\DomainException');

        if (\method_exists($this, 'expectExceptionMessageMatches')) {
            $this->expectExceptionMessageMatches('/ must be an instance of Exception or Throwable$/');
        }

        Convert::to(new \InvalidArgumentException, function () {});
    }

    public function testMissingTwoArguments()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerExceptionWithMessage('Mpyw\Exceper\Convert::to() expects at least 2 parameters, 0 given');

        Convert::to();
    }

    public function testMissingOneArgument()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerExceptionWithMessage('Mpyw\Exceper\Convert::to() expects at least 2 parameters, 1 given');

        Convert::to('dummy');
    }

    public function testInvalidFirstArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerExceptionWithMessage('Mpyw\Exceper\Convert::to() expects parameter 1 to be string, array given');

        Convert::to(array(), function () {});
    }

    public function testInvalidSecondArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerExceptionWithMessage('Mpyw\Exceper\Convert::to() expects parameter 2 to be callable or integer, string given');

        Convert::to('dummy', 'dummy');
    }

    public function testInvalidThirdArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerExceptionWithMessage('Mpyw\Exceper\Convert::to() expects parameter 3 to be integer, string given');

        Convert::to('dummy', function () {}, 'dummy');
    }
}
