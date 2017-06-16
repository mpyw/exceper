<?php

namespace mpyw\Exceper;

/**
 * Class Exceper
 * @package mpyw\Exceper
 * @method static mixed to(string $class, callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toError(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toParseError(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toTypeError(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toArgumentCountError(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toArithmeticError(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toDivisionByZeroError(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toClosedGeneratorException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toDOMException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toLogicException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toBadFunctionCallException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toBadMethodCallException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toDomainException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toInvalidArgumentException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toLengthException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toOutOfRangeException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toRuntimeException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toOutOfBoundsException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toOverflowException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toRangeException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toUnderflowException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toUnexpectedValueException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toAssertionError(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toPDOException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toPharException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toReflectionException(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed tomysqli_sql_exception(callable $callback, int $types = E_ALL | E_STRICT)
 * @method static mixed toSoapFault(callable $callback, int $types = E_ALL | E_STRICT)
 */
class Convert
{
    /**
     * @param string $method
     * @param array $args
     * @param int $required
     */
    protected static function validateArgumentTypes($method, array $args, $required)
    {
        $argc = count($args);
        if ($argc < $required) {
            throw new \InvalidArgumentException(get_called_class() . "::$method() expects at least $required parameters, $argc given");
        }
        if ($required === 2 and !is_string($args[0]) and !is_object($args[0]) || !method_exists($args[0], '__toString')) {
            throw new \InvalidArgumentException(get_called_class() . "::$method() expects parameter 1 to be string, " . gettype($args[0]) . ' given');
        }
        if (!is_callable($args[$required - 1])) {
            throw new \InvalidArgumentException(get_called_class() . "::$method() expects parameter $required to be callable, " . gettype($args[$required - 1]) . ' given');
        }
        if (isset($args[$required]) && !is_numeric($args[$required])) {
            throw new \InvalidArgumentException(get_called_class() . "::$method() expects parameter " . ($required + 1) . ' to be integer, ' . gettype($args[$required]) . ' given');
        }
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function __callStatic($method, array $args)
    {
        if (strtolower(substr($method, 0, 2)) !== 'to') {
            throw new \BadMethodCallException('Call to undefined method ' . get_called_class() . "::$method()");
        }

        $class = (string)substr($method, 2);
        $required = $class === '' ? 2 : 1;
        self::validateArgumentTypes($method, $args, $required);

        if ($class === '') {
            $class = (string)array_shift($args);
        }

        if (!class_exists($class) and !is_subclass_of($class, '\Exception') and \PHP_VERSION < 7 || !is_subclass_of($class, '\Throwable')) {
            throw new \DomainException("The class \"$class\" must be an instance of Exception or Throwable");
        }

        return Core::handle($args[0], function ($severity, $message, $file, $line) use ($class, $args) {
            if (!(error_reporting() & $severity)) {
                return;
            }
            throw Core::rewriteLocation(new $class($message), $file, $line);
        }, isset($args[1]) ? (int)$args[1] : \E_ALL | \E_STRICT);
    }

    /**
     * @param callable $callback
     * @param int $types
     * @return mixed
     */
    public static function toErrorException($callback, $types = null)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(get_called_class() . '::' . __METHOD__ . '() expects parameter 1 to be callable, ' . gettype($callback) . ' given');
        }
        if ($types !== null && !is_numeric($types)) {
            throw new \InvalidArgumentException(get_called_class() . '::' . __METHOD__ . '() expects parameter 2 to be integer, ' . gettype($types) . ' given');
        }

        return Core::handle($callback, function ($severity, $message, $file, $line) {
            if (!(error_reporting() & $severity)) {
                return;
            }
            throw new \ErrorException($message, 0, $severity, $file, $line);
        }, (int)($types === null ? \E_ALL | \E_STRICT : $types));
    }

    /**
     * @param callable $callback
     * @param int $types
     * @param bool $captureExceptions
     * @return mixed
     */
    public static function silent($callback, $types = null, $captureExceptions = true)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(get_called_class() . '::' . __METHOD__ . '() expects parameter 1 to be callable, ' . gettype($callback) . ' given');
        }
        if ($types !== null && !is_numeric($types)) {
            throw new \InvalidArgumentException(get_called_class() . '::' . __METHOD__ . '() expects parameter 2 to be integer, ' . gettype($types) . ' given');
        }

        $dummyException = new \Exception;
        try {
            return Core::handle($callback, function ($severity) use ($dummyException) {
                if (!(error_reporting() & $severity)) {
                    return;
                }
                throw $dummyException;
            }, (int)($types === null ? \E_ALL | \E_STRICT : $types));
        } catch (\Exception $e) {
        } catch (\Throwable $e) {
        }
        if (!$captureExceptions && $e !== $dummyException) {
            throw $e;
        }
    }
}
