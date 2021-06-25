<?php

namespace Mpyw\Exceper\Tests;

use Mpyw\Exceper\Convert;
use PHPUnit\Framework\Exception as PHPUnitFrameworkException;

class ConvertWithInlineClassNameTest extends TestCase
{
    public function testFlow()
    {
        Convert::toInvalidArgumentException(function () {
        });

        $this->shouldTriggerWarning();
        $this->shouldTriggerWarningWithMessage($this->uninitializedStringOffsetMessage());
        echo $this->string[10];
    }

    public function testFlowWithException()
    {
        try {
            $that = $this;
            Convert::toInvalidArgumentException(function () use (&$line, $that) {
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

    /**
     * @requires PHP 7.0.0
     */
    public function testFlowWithError()
    {
        try {
            $that = $this;
            Convert::toArithmeticError(function () use (&$line, $that) {
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

    public function testMissingOneArgument()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerWarningWithMessage('Mpyw\Exceper\Convert::toInvalidArgumentException() expects at least 1 parameters, 0 given');

        Convert::toInvalidArgumentException();
    }

    public function testInvalidFirstArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerWarningWithMessage('Mpyw\Exceper\Convert::toInvalidArgumentException() expects parameter 1 to be callable or integer, string given');

        Convert::toInvalidArgumentException('dummy');
    }

    public function testInvalidSecondArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerWarningWithMessage('Mpyw\Exceper\Convert::toInvalidArgumentException() expects parameter 2 to be integer, string given');

        Convert::toInvalidArgumentException(function () {}, 'dummy');
    }
}
