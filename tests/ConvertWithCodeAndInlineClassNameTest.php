<?php

namespace Mpyw\Exceper\Tests;

use Mpyw\Exceper\Convert;

class ConvertWithCodeAndInlineClassNameTest extends TestCase
{
    public function testFlow()
    {
        Convert::toInvalidArgumentException(114514, function () {
        });

        $this->shouldTriggerWarning();
        $this->shouldTriggerWarningWithMessage($this->uninitializedStringOffsetMessage());
        echo $this->string[10];
    }

    /**
     * @requires PHP 7.0.0
     */
    public function testFlowWithException()
    {
        try {
            $that = $this;
            Convert::toInvalidArgumentException(114514, function () use (&$line, $that) {
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

    /**
     * @requires PHP 7.0.0
     */
    public function testFlowWithError()
    {
        try {
            $that = $this;
            Convert::toArithmeticError(114514, function () use (&$line, $that) {
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

    public function testMissingOneArgument()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerWarningWithMessage('Mpyw\Exceper\Convert::toInvalidArgumentException() expects parameter 2 to be callable, none given');

        Convert::toInvalidArgumentException(114514);
    }

    public function testInvalidSecondArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerWarningWithMessage('Mpyw\Exceper\Convert::toInvalidArgumentException() expects parameter 2 to be callable, NULL given');

        Convert::toInvalidArgumentException(114514, null);
    }

    public function testInvalidThirdArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerWarningWithMessage('Mpyw\Exceper\Convert::toInvalidArgumentException() expects parameter 3 to be integer, string given');

        Convert::toInvalidArgumentException(114514, function () {}, 'dummy');
    }
}
