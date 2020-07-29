<?php declare(strict_types=1);

namespace StudyPortals\CMS;

use ErrorException;
use Rollbar\Rollbar;
use RuntimeException;
use Throwable;

/**
 * Convert all PHP errors into ErrorException and handle uncaught exceptions
 * by raising a HTTP/1.1 500.
 */

abstract class ExceptionHandler
{

    /**
     * Enable custom exception- and error-handling.
     *
     * @return void
     */

    public static function enable(): void
    {
        set_exception_handler(__CLASS__ . '::exception');
        set_error_handler(__CLASS__ . '::error', error_reporting());
    }

    /**
     * Trigger/log a notice.
     *
     * Log a notice towards Rollbar and in case DEBUG_MODe is enabled generate
     * a RuntimeException.
     *
     * @param string $message
     *
     * @return void
     * @throws RuntimeException
     */

    public static function notice(string $message): void
    {
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            throw new RuntimeException($message);
        }

        Rollbar::notice($message);
    }

    /**
     * Raise a HTTP/1.1 500 status in case of an uncaught exception.
     *
     * @param Throwable $Throwable
     *
     * @return void
     */

    public static function exception(Throwable $Throwable): void
    {
        if (defined('DEBUG_MODE') && !DEBUG_MODE) {
            Rollbar::error($Throwable);
        }

        @header('HTTP/1.1 500 Internal Server Error');
        @header('Content-Type: text/plain');

        echo 'Something went seriously wrong; please try again later...';
    }

    /**
     * Convert PHP-errors into ErrorException.
     *
     * @param integer       $severity
     * @param string        $message
     * @param string        $file
     * @param integer       $line
     * @param array<string> $context
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws ErrorException
     */

    public static function error(
        $severity,
        $message,
        $file,
        $line,
        $context
    ): bool {
        // Respect the "@" error suppression operator
        if (error_reporting() === 0) {
            return true;
        }

        if ($severity && error_reporting()) {
            $recoverable = [
                E_WARNING,
                E_NOTICE,
                E_USER_WARNING,
                E_USER_NOTICE,
                E_STRICT,
                E_DEPRECATED,
                E_USER_DEPRECATED,
            ];

            // Only for non-fatal errors
            if (in_array($severity, $recoverable, true)) {
                return true;
            }

            throw new ErrorException(
                $message,
                0,
                $severity,
                $file,
                $line
            );
        }

        return true;
    }
}
