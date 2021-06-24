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
            Convert::toInvalidArgumentException(function () use (&$line) {
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
            Convert::toArithmeticError(function () use (&$line) {
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

    public function testMissingOneArgument()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerExceptionWithMessage('Mpyw\Exceper\Convert::toInvalidArgumentException() expects at least 1 parameters, 0 given');

        Convert::toInvalidArgumentException();
    }

    public function testInvalidFirstArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerExceptionWithMessage('Mpyw\Exceper\Convert::toInvalidArgumentException() expects parameter 1 to be callable or integer, string given');

        Convert::toInvalidArgumentException('dummy');
    }

    public function testInvalidSecondArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerExceptionWithMessage('Mpyw\Exceper\Convert::toInvalidArgumentException() expects parameter 2 to be integer, string given');

        Convert::toInvalidArgumentException(function () {}, 'dummy');
    }
}
