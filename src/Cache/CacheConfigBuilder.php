<?php declare(strict_types=1);

namespace StudyPortals\Cache;

use StudyPortals\Cache\Config\File as FileCacheConfig;
use StudyPortals\Cache\Config\Memcache as MemcacheCacheConfig;
use StudyPortals\Utils\IConfig;

/**
 * @SuppressWarnings(PHPMD)
 */
abstract class CacheConfigBuilder
{

    /**
     * @param IConfig $config
     *
     * @return CacheConfig
     * @throws CacheConfigException
     */
    public static function build(IConfig $config): CacheConfig
    {

        $engine = trim($config->retrieve('CACHE_ENGINE'));

        switch ($engine) {
            case 'File':
                return new FileCacheConfig(
                    $config->retrieve('CACHE_STORE')
                );

            case 'Memcache':
                return new MemcacheCacheConfig(
                    $config->retrieve('CACHE_STORE')
                );

            default:
                throw new CacheConfigException(
                    "Invalid (or no) cache-engine specified: '$engine'"
                );
        }
    }
}
