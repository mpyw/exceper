# Exceper [![Build Status](https://github.com/mpyw/exceper/actions/workflows/test.yml/badge.svg?branch=master)](https://github.com/mpyw/exceper/actions) [![Coverage Status](https://coveralls.io/repos/github/mpyw/exceper/badge.svg?branch=master)](https://coveralls.io/github/mpyw/exceper?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mpyw/exceper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mpyw/exceper/?branch=master)

Provides temporary error handler automatically using [`set_error_handler()`] and [`restore_error_handler()`].

| PHP | :question: | Feature Restriction |
|:---:|:---:|:---:|
| 5.3.2~ | :smile: | Supported |
| ~5.3.1 | :boom: | Incompatible |

## Installing

```
composer require mpyw/exceper
```

## Quick Example

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Mpyw\Exceper\Convert;

try {

    // We pick errors triggered by fopen() or fgets().
    // They are converted into \RuntimeException.
    Convert::toRuntimeException(function () {
        $fp = fopen('http://example.com', 'rb');
        while (false !== $line = fgets($fp)) {
            echo $line;
        }
    });

} catch (\RuntimeException $e) {

    // Do something here

}

// Error handler is already automatically restored.
```

## API

### Mpyw\Exceper\Convert::to()<br>Mpyw\Exceper\Convert::to`{$class}`()

Capture errors to convert into an instance of the specified class.  

- Error handlers are automatically restored.
- **`->getFile()` and `->getLine()` returns correct locations** like `ErrorException` does.

```php
static Mpyw\Exceper\Convert::to(string $class, callable $callback, int $types = E_ALL | E_STRICT): mixed
static Mpyw\Exceper\Convert::to{$class}(callable $callback, int $types = E_ALL | E_STRICT): mixed
static Mpyw\Exceper\Convert::to(string $class, int $code, callable $callback, int $types = E_ALL | E_STRICT): mixed
static Mpyw\Exceper\Convert::to{$class}(int $code, callable $callback, int $types = E_ALL | E_STRICT): mixed
```

#### Arguments

- **`(string)`** __*$class*__<br /> Conversion target class name which is an instance of `\Exception` or `\Throwable`. Please note that case-sensitivity of class name depends on the implementation of your autoloaders, which can cause an unexpected behavior if the target class is not loaded.
- **`(int)`** __*$code*__<br /> Error code passed to the constructor as the second argument. Default to `0`.
- **`(callable)`** __*$callback*__<br /> Callback function to be executed. This parameter SHOULD be used as `\Closure` because arguments cannot be specified.
- **`(int)`** __*$types*__<br /> Bit mask of target error severities.

#### Return Value

**`(mixed)`**<br />Propagates upcoming value from `$callback()`.

#### Exception

Converted errors.

### Mpyw\Exceper\Convert::silent()

Capture errors but never throw anything.

```php
static Mpyw\Exceper\Convert::silent(callable $callback, int $types = E_ALL | E_STRICT, bool $captureExceptions = true): mixed
```

#### Arguments

- **`(callable)`** __*$callback*__<br /> Callback function to be executed. This parameter SHOULD be a `\Closure` because arguments cannot be specified.
- **`(int)`** __*$types*__<br /> Bit mask of target error severities.
- **`(bool)`** __*$captureExceptions*__<br /> Capture exceptions that were directly thrown; not originated in `set_error_handler()`.

#### Return Value

**`(mixed)`**<br />Propagates upcoming value from `$callback()`. Returns `null` if something thrown.

[`set_error_handler()`]: http://www.php.net/manual/function.set-error-handler.php
[`restore_error_handler()`]: http://www.php.net/manual/function.restore-error-handler.php
