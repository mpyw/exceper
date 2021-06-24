<?php

namespace Mpyw\Exceper\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @before
     */
    public function prepare()
    {
        \set_error_handler(function($severity, $message, $file, $line) {
            throw new Warning($message);
        });
    }

    /**
     * @after
     */
    public function terminate()
    {
        \restore_error_handler();
    }

    public function shouldTriggerException($exception)
    {
        if (\method_exists($this, 'expectException')) {
            $this->expectException($exception);
            return;
        }

        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException($exception);
        }
    }

    public function shouldTriggerExceptionWithMessage($message)
    {
        if (\method_exists($this, 'expectExceptionMessage')) {
            $this->expectExceptionMessage($message);
        }
    }

    public function shouldTriggerFopenWarning()
    {
        if (\version_compare(PHP_VERSION, '8', '>=')) {
            $this->expectError();
            return;
        }

        if (\method_exists($this, 'expectException')) {
            $this->expectException('\Mpyw\Exceper\Tests\Warning');
            return;
        }

        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('\Mpyw\Exceper\Tests\Warning');
        }
    }

    public function shouldTriggerFopenWarningMessage($message)
    {
        $this->shouldTriggerExceptionWithMessage($message);
    }

    public function fopenErrorMessage()
    {
        return \version_compare(PHP_VERSION, '8', '>=')
            ? 'fopen() expects at least 2 arguments, 0 given'
            : 'fopen() expects at least 2 parameters, 0 given';
    }
}
