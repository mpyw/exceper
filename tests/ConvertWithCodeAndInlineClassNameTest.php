<?php

namespace Mpyw\Exceper\Tests;

use Mpyw\Exceper\Convert;

class ConvertWithCodeAndInlineClassNameTest extends TestCase
{
    public function testFlow()
    {
        Convert::toInvalidArgumentException(114514, function () {
        });

        $this->shouldTriggerFopenWarning();
        $this->shouldTriggerExceptionWithMessage($this->fopenErrorMessage());
        fopen();
    }

    /**
     * @requires PHP 7.0.0
     */
    public function testFlowWithException()
    {
        if (\version_compare(PHP_VERSION, '8', '>=')) {
            $this->shouldTriggerFopenWarning();
            $this->shouldTriggerFopenWarningMessage($this->fopenErrorMessage());
        }

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
        $this->assertEquals($this->fopenErrorMessage(), $x->getMessage());

        $this->shouldTriggerFopenWarning();
        $this->shouldTriggerExceptionWithMessage($this->fopenErrorMessage());
        fopen();
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
            Convert::toArithmeticError(114514, function () use (&$line) {
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

    public function testMissingOneArgument()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerExceptionWithMessage('Mpyw\Exceper\Convert::toInvalidArgumentException() expects parameter 2 to be callable, none given');

        Convert::toInvalidArgumentException(114514);
    }

    public function testInvalidSecondArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerExceptionWithMessage('Mpyw\Exceper\Convert::toInvalidArgumentException() expects parameter 2 to be callable, NULL given');

        Convert::toInvalidArgumentException(114514, null);
    }

    public function testInvalidThirdArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerExceptionWithMessage('Mpyw\Exceper\Convert::toInvalidArgumentException() expects parameter 3 to be integer, string given');

        Convert::toInvalidArgumentException(114514, function () {}, 'dummy');
    }
}
