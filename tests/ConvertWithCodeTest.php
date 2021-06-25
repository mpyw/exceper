<?php

namespace Mpyw\Exceper\Tests;

use Mpyw\Exceper\Convert;

class ConvertWithCodeTest extends TestCase
{
    public function testFlow()
    {
        $that = $this;
        Convert::to('\InvalidArgumentException', 114514, function () use ($that) {
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
            Convert::to('\InvalidArgumentException', 114514, function () use (&$line, $that) {
                $line = __LINE__; echo $that->string[10];
            });
        } catch (\InvalidArgumentException $x) {
        }
        $this->assertTrue(isset($x));
        $this->assertEquals(114514, $x->getCode());
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
            Convert::to('\InvalidArgumentException', 114514, function () use ($that) {
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
            Convert::to('\ArithmeticError', 114514, function () use (&$line, $that) {
                $line = __LINE__; echo $that->string[10];
            });
        } catch (\ArithmeticError $x) {
        }
        $this->assertTrue(isset($x));
        $this->assertEquals(114514, $x->getCode());
        $this->assertEquals(__FILE__, $x->getFile());
        $this->assertEquals($line, $x->getLine());
        $this->assertEquals($this->uninitializedStringOffsetMessage(), $x->getMessage());

        $this->shouldTriggerWarning();
        $this->shouldTriggerWarningWithMessage($this->uninitializedStringOffsetMessage());
        echo $this->string[10];
    }

    public function testStringableInstanceAsClassName()
    {
        $this->shouldTriggerException('\DomainException');

        if (\method_exists($this, 'expectExceptionMessageMatches')) {
            $this->expectExceptionMessageMatches('/ must be an instance of Exception or Throwable$/');
        }

        Convert::to(new \InvalidArgumentException, 114514, function () {});
    }

    public function testMissingOneArgument()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerWarningWithMessage('Mpyw\Exceper\Convert::to() expects parameter 3 to be callable, none given');

        Convert::to('dummy', 114514);
    }

    public function testInvalidFirstArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerWarningWithMessage('Mpyw\Exceper\Convert::to() expects parameter 1 to be string, array given');

        Convert::to(array(), 114514, function () {});
    }

    public function testInvalidThridArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerWarningWithMessage('Mpyw\Exceper\Convert::to() expects parameter 3 to be callable, NULL given');

        Convert::to('dummy', 114514, null);
    }

    public function testInvalidFourthArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerWarningWithMessage('Mpyw\Exceper\Convert::to() expects parameter 4 to be integer, string given');

        Convert::to('dummy', 114514, function () {}, 'dummy');
    }
}
