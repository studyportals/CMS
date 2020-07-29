<?php declare(strict_types=1);

namespace StudyPortals\CMS\Site;

use StudyPortals\Cache\CacheEngine;

trait SiteCache
{
    /**
     * @var CacheEngine
     */
    private $cacheEngine;

    /**
     * @var bool
     */
    private $enabled = true;

    public function enableCache(): void
    {
        $this->enabled = true;

        if ($this->cacheEngine instanceof CacheEngine) {
            $this->cacheEngine->enable(true);
        }
    }
    public function disableCache(): void
    {
        $this->enabled = false;

        if ($this->cacheEngine instanceof CacheEngine) {
            $this->cacheEngine->enable(false);
        }
    }

    public function getCache(): CacheEngine
    {
        return $this->cacheEngine;
    }

    public function buildCache(ICacheBuilder $builder): void
    {
        $this->cacheEngine = $builder->build($this->Config);
        $this->cacheEngine->enable($this->isCacheEnabled());
    }

    public function isCacheEnabled(): bool
    {
        return $this->enabled;
    }
}
