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

        $this->shouldTriggerWarning();
        $this->shouldTriggerWarningWithMessage($this->uninitializedStringOffsetMessage());
        echo $this->string[10];
    }

    public function testFlowWithException()
    {
        try {
            $that = $this;
            Convert::to('\InvalidArgumentException', function () use (&$line, $that) {
                $line = __LINE__; echo $that->string[10];
            });
        } catch (\InvalidArgumentException $x) {
        }
        $this->assertTrue(isset($x));
        $this->assertEquals(0, $x->getCode());
        $this->assertEquals(__FILE__, $x->getFile());
        $this->assertEquals($line, $x->getLine());
        $this->assertEquals($this->uninitializedStringOffsetMessage(), $x->getMessage());

        $this->shouldTriggerWarning();
        $this->shouldTriggerWarningWithMessage($this->uninitializedStringOffsetMessage());
        echo $this->string[10];
    }

    public function testSuppressingErrors()
    {
        try {
            $that = $this;
            Convert::to('\InvalidArgumentException', function () use ($that) {
                echo @$that->string[10];
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
            $that = $this;
            Convert::to('\ArithmeticError', function () use (&$line, $that) {
                $line = __LINE__; echo $that->string[10];
            });
        } catch (\ArithmeticError $x) {
        }
        $this->assertTrue(isset($x));
        $this->assertEquals(0, $x->getCode());
        $this->assertEquals(__FILE__, $x->getFile());
        $this->assertEquals($line, $x->getLine());
        $this->assertEquals($this->uninitializedStringOffsetMessage(), $x->getMessage());

        $this->shouldTriggerWarning();
        $this->shouldTriggerWarningWithMessage($this->uninitializedStringOffsetMessage());
        echo $this->string[10];
    }

    public function testInvalidMethodName()
    {
        $this->shouldTriggerException('\BadMethodCallException');
        $this->shouldTriggerWarningWithMessage('Call to undefined method Mpyw\Exceper\Convert::dummy()');

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
        $this->shouldTriggerWarningWithMessage('Mpyw\Exceper\Convert::to() expects at least 2 parameters, 0 given');

        Convert::to();
    }

    public function testMissingOneArgument()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerWarningWithMessage('Mpyw\Exceper\Convert::to() expects at least 2 parameters, 1 given');

        Convert::to('dummy');
    }

    public function testInvalidFirstArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerWarningWithMessage('Mpyw\Exceper\Convert::to() expects parameter 1 to be string, array given');

        Convert::to(array(), function () {});
    }

    public function testInvalidSecondArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerWarningWithMessage('Mpyw\Exceper\Convert::to() expects parameter 2 to be callable or integer, string given');

        Convert::to('dummy', 'dummy');
    }

    public function testInvalidThirdArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerWarningWithMessage('Mpyw\Exceper\Convert::to() expects parameter 3 to be integer, string given');

        Convert::to('dummy', function () {}, 'dummy');
    }
}
