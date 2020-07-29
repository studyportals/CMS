<?php declare(strict_types=1);

namespace StudyPortals\Cache;

use Exception;
use Psr\SimpleCache\CacheException;

/**
 * @SuppressWarnings(PHPMD)
 */
class PsrCacheException extends Exception implements CacheException
{
}
