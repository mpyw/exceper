<?php

use mpyw\Exceper\Convert;

class ConvertToErrorExceptionTest extends \Codeception\TestCase\Test
{
    public function testFlow()
    {
        Convert::toErrorException(function () {
        });
        try {
            fopen();
        } catch (\PHPUnit_Framework_Exception $x) {
        }
        $this->assertTrue(isset($x));
        $this->assertEquals('fopen() expects at least 2 parameters, 0 given', $x->getMessage());
    }

    public function testFlowWithException()
    {
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
        $this->assertEquals('fopen() expects at least 2 parameters, 0 given', $x->getMessage());
        try {
            fopen();
        } catch (\PHPUnit_Framework_Exception $y) {
        }
        $this->assertTrue(isset($y));
        $this->assertEquals('fopen() expects at least 2 parameters, 0 given', $y->getMessage());
    }
}
