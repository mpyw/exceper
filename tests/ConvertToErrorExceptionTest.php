<?php

namespace Mpyw\Exceper\Tests;

use Mpyw\Exceper\Convert;

class ConvertToErrorExceptionTest extends TestCase
{
    public function testFlow()
    {
        Convert::toErrorException(function () {
        });

        $this->shouldTriggerWarning();
        $this->shouldTriggerWarningWithMessage($this->uninitializedStringOffsetMessage());
        echo $this->string[10];
    }

    public function testFlowWithException()
    {
        try {
            $that = $this;
            Convert::toErrorException(function () use (&$line, $that) {
                $line = __LINE__; echo $that->string[10];
            });
        } catch (\ErrorException $x) {
        }
        $this->assertTrue(isset($x));
        $this->assertEquals(0, $x->getCode());
        $this->assertEquals(__FILE__, $x->getFile());
        $this->assertEquals($line, $x->getLine());
        $this->assertTrue(\in_array($x->getSeverity(), array(\E_WARNING, \E_NOTICE)));
        $this->assertEquals($this->uninitializedStringOffsetMessage(), $x->getMessage());

        $this->shouldTriggerWarning();
        $this->shouldTriggerWarningWithMessage($this->uninitializedStringOffsetMessage());
        echo $this->string[10];
    }
}
