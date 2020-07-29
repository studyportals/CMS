<?php declare(strict_types=1);

namespace StudyPortals\CMS\Site;

use StudyPortals\Cache\CacheEngine;
use StudyPortals\Cache\CacheException;
use StudyPortals\Utils\IConfig;

class CacheBuilder implements ICacheBuilder
{

    public function build(IConfig $config): CacheEngine
    {
        try {
            return CacheEngine::constructCacheEngine($config);
        } catch (CacheException $e) {
            throw new SiteException(
                'Unable to buildDefaultCache',
                0,
                $e
            );
        }
    }
}
