<?php declare(strict_types=1);

namespace StudyPortals\Cache;

use Exception;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * @SuppressWarnings(PHPMD)
 */
class PsrInvalidArgumentException extends Exception implements
    InvalidArgumentException
{
}
