<?php

namespace Mpyw\Exceper\Tests;

use Mpyw\Exceper\Convert;

class ConvertSilentTest extends TestCase
{
    public function testFlowWithException()
    {
        $count = 0;

        $that = $this;
        $this->assertNull(Convert::silent(function () use (&$count, $that) {
            ++$count;
            echo $that->string[10];
            ++$count;
            echo $that->string[10];
            ++$count;
        }));
        $this->assertEquals(1, $count);

        $this->shouldTriggerWarning();
        $this->shouldTriggerWarningWithMessage($this->uninitializedStringOffsetMessage());
        echo $this->string[10];
    }

    public function testFlowWithExceptionWithNonCaptureMode()
    {
        $count = 0;

        $that = $this;
        $this->assertNull(Convert::silent(function () use (&$count, $that) {
            ++$count;
            echo $that->string[10];
            ++$count;
            echo $that->string[10];
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

        $that = $this;
        $this->assertNull(Convert::silent(function () use (&$count, $that) {
            ++$count;
            echo @$that->string[10];
            ++$count;
            echo $that->string[10];
            ++$count;
        }));
        $this->assertEquals(2, $count);
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

        $this->shouldTriggerWarning();
        $this->shouldTriggerWarningWithMessage($this->uninitializedStringOffsetMessage());
        echo $this->string[10];
    }

    public function testInvalidFirstArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerWarningWithMessage('Mpyw\Exceper\Convert::silent() expects parameter 1 to be callable, string given');

        Convert::silent('dummy');
    }

    public function testInvalidSecondArgumentType()
    {
        $this->shouldTriggerException('\InvalidArgumentException');
        $this->shouldTriggerWarningWithMessage('Mpyw\Exceper\Convert::silent() expects parameter 2 to be integer, string given');

        Convert::silent(function () {
        }, 'dummy');
    }
}
