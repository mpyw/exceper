<?php

namespace Mpyw\Exceper\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public $string = '.';

    /**
     * @before
     */
    public function prepare()
    {
        \set_error_handler(function($severity, $message, $file, $line) {
            // Warning: >=8.0
            // Notice: <8.0
            throw new Warning($message);
        }, \E_WARNING | \E_NOTICE);
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

    public function shouldTriggerWarningWithMessage($message)
    {
        if (\method_exists($this, 'expectExceptionMessage')) {
            $this->expectExceptionMessage($message);
        }
    }

    public function shouldTriggerWarning()
    {
        if (\method_exists($this, 'expectException')) {
            $this->expectException('\Mpyw\Exceper\Tests\Warning');
            return;
        }

        if (\method_exists($this, 'setExpectedException')) {
            $this->setExpectedException('\Mpyw\Exceper\Tests\Warning');
        }
    }

    public function uninitializedStringOffsetMessage()
    {
        return \version_compare(PHP_VERSION, '8', '>=')
            ? 'Uninitialized string offset 10'
            : 'Uninitialized string offset: 10';
    }
}
