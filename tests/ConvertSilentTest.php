<?php

namespace Mpyw\Exceper\Tests;

use Mpyw\Exceper\Convert;

class ConvertSilentTest extends TestCase
{
    public function testFlowWithException()
    {
        $count = 0;

        $this->assertNull(Convert::silent(function () use (&$count) {
            ++$count;
            fopen();
            ++$count;
            fopen();
            ++$count;
        }));
        $this->assertEquals(1, $count);

        $this->shouldTriggerFopenWarning();
        $this->shouldTriggerExceptionWithMessage($this->fopenErrorMessage());
        fopen();
    }

    public function testFlowWithExceptionWithNonCaptureMode()
    {
        if (\version_compare(PHP_VERSION, '8', '>=')) {
            $this->shouldTriggerFopenWarning();
            $this->shouldTriggerFopenWarningMessage($this->fopenErrorMessage());
        }

        $count = 0;

        $this->assertNull(Convert::silent(function () use (&$count) {
            ++$count;
            fopen();
            ++$count;
            fopen();
            ++$count;
        }, null, false));
        $this->assertEquals(1, $count);

        try {
            $this->assertNull(Convert::silent(function () use (&$count) {
                ++$count;
                throw new \InvalidArgumentException('yo');
            }, null, false));
        } catch (\InvalidArgumentException $x) {
        }
        $this->assertTrue(isset($x));
        $this->assertEquals('yo', $x->getMessage());
        $this->assertEquals(2, $count);
    }

    public function testSuppressingErrors()
    {
        $count = 0;
        $this->assertNull(Convert::silent(function () use (&$count) {
            ++$count;
            @fopen();
            ++$count;
            fopen();
            ++$count;
        }));
        $this->assertEquals(
            \version_compare(PHP_VERSION, '8', '>=')
                ? 1
                : 2,
            $count
        );
    }

    /**
     * @requires PHP 7.0.0
     */
    public function testFlowWithError()
    {
        $count = 0;
        $this->assertNull(Convert::silent(function () use (&$count) {
            ++$count;
            0 << -1;
            ++$count;
            0 << -1;
            ++$count;
        }));
        $this->assertEquals(1, $count);

        $this->shouldTriggerFopenWarning();
        $this->shouldTriggerExceptionWithMessage($this->fopenErrorMessage());
        fopen();
    }

    public function testInvalidFirstArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerExceptionWithMessage('Mpyw\Exceper\Convert::silent() expects parameter 1 to be callable, string given');

        Convert::silent('dummy');
    }

    public function testInvalidSecondArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerExceptionWithMessage('Mpyw\Exceper\Convert::silent() expects parameter 2 to be integer, string given');

        Convert::silent(function () {
        }, 'dummy');
    }
}
