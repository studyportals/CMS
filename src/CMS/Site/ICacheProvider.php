<?php declare(strict_types=1);

namespace StudyPortals\CMS\Site;

use StudyPortals\Cache\CacheEngine;

interface ICacheProvider
{
    public function getCache(): CacheEngine;

    public function enableCache(): void;

    public function disableCache(): void;

    public function isCacheEnabled(): bool;

    public function buildCache(ICacheBuilder $builder): void;
}
