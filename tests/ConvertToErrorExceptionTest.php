<?php

namespace Mpyw\Exceper\Tests;

use Mpyw\Exceper\Convert;

class ConvertToErrorExceptionTest extends TestCase
{
    public function testFlow()
    {
        Convert::toErrorException(function () {
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
            Convert::toErrorException(function () use (&$line) {
                $line = __LINE__; fopen();
            });
        } catch (\ErrorException $x) {
        }
        $this->assertTrue(isset($x));
        $this->assertEquals(0, $x->getCode());
        $this->assertEquals(__FILE__, $x->getFile());
        $this->assertEquals($line, $x->getLine());
        $this->assertEquals(E_WARNING, $x->getSeverity());
        $this->assertEquals($this->fopenErrorMessage(), $x->getMessage());

        $this->shouldTriggerFopenWarning();
        $this->shouldTriggerExceptionWithMessage($this->fopenErrorMessage());
        fopen();
    }
}
