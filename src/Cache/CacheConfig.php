<?php declare(strict_types=1);

namespace StudyPortals\Cache;

/**
 * @SuppressWarnings(PHPMD)
 */
interface CacheConfig
{

    public function __construct(string $store);
}
