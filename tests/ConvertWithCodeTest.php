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
            Convert::to('\InvalidArgumentException', 114514, function () use (&$line) {
                $line = __LINE__; fopen();
            });
        } catch (\InvalidArgumentException $x) {
        }
        $this->assertTrue(isset($x));
        $this->assertEquals(114514, $x->getCode());
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
        if (\version_compare(PHP_VERSION, '8', '>=')) {
            $this->shouldTriggerFopenWarning();
            $this->shouldTriggerFopenWarningMessage($this->fopenErrorMessage());
        }

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
        $this->assertEquals($this->fopenErrorMessage(), $x->getMessage());

        $this->shouldTriggerFopenWarning();
        $this->shouldTriggerExceptionWithMessage($this->fopenErrorMessage());
        fopen();
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
        $this->shouldTriggerExceptionWithMessage('Mpyw\Exceper\Convert::to() expects parameter 3 to be callable, none given');

        Convert::to('dummy', 114514);
    }

    public function testInvalidFirstArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerExceptionWithMessage('Mpyw\Exceper\Convert::to() expects parameter 1 to be string, array given');

        Convert::to(array(), 114514, function () {});
    }

    public function testInvalidThridArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerExceptionWithMessage('Mpyw\Exceper\Convert::to() expects parameter 3 to be callable, NULL given');

        Convert::to('dummy', 114514, null);
    }

    public function testInvalidFourthArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerExceptionWithMessage('Mpyw\Exceper\Convert::to() expects parameter 4 to be integer, string given');

        Convert::to('dummy', 114514, function () {}, 'dummy');
    }
}
