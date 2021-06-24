<?php

namespace Mpyw\Exceper;

/**
 * Class Core
 */
class Core
{
    /**
     * @param callable $callback
     * @param callable $handler
     * @param int $types
     * @return mixed
     * @throws \Exception|\Throwable
     */
    public static function handle($callback, $handler, $types = null)
    {
        \set_error_handler($handler, $types === null ? \E_ALL | \E_STRICT : $types);

        try {
            $result = \call_user_func($callback);
            \restore_error_handler();
            return $result;
        } catch (\Exception $e) {
        } catch (\Throwable $e) {
        }

        \restore_error_handler();
        throw $e;
    }

    /**
     * @param \Exception|\Throwable $e
     * @param string $file
     * @param int $line
     * @return \Exception|\Throwable $e
     */
    public static function rewriteLocation($e, $file, $line)
    {
        $rc = new \ReflectionClass($e);

        $rpf = $rc->getProperty('file');
        $rpl = $rc->getProperty('line');

        $rpf->setAccessible(true);
        $rpl->setAccessible(true);

        $rpf->setValue($e, $file);
        $rpl->setValue($e, $line);

        return $e;
    }
}
