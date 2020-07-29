<?php declare(strict_types=1);

namespace StudyPortals\Cache;

use Serializable;
use StudyPortals\Cache\Config\File as FileCacheConfig;
use StudyPortals\Cache\Config\Memcache as MemcacheCacheConfig;
use StudyPortals\Utils\IConfig;

/**
 * @SuppressWarnings(PHPMD)
 * @codeCoverageIgnore
 */
abstract class CacheEngine implements Cache, Serializable
{

    /**
     * A toggle for whether this CacheEngine is enabled.
     *
     * <p>If <em>false</em>, all data is retrieved fresh (c.q. all calls to
     * {@link CacheEngine::__get()} will return <em>null</em>.</p>
     *
     * @var boolean
     */
    protected $enabled = true;

    /**
     * Construct a new CacheEngine.
     *
     * <p>Under regular circumstances (c.q. when you're operating in an active
     * Site context) you should <strong>never</strong> call this method; always
     * use the Site::$Cache property. Calling this method will open a new
     * connection to the CacheStore which is completely unnecessary.<br>
     * This method is intended as a helper in cases (c.q. Packer) where the
     * CacheEngine is required without the rest of Site being preset.</p>
     *
     * @param IConfig $config
     * @param string  $prefix
     *
     * @return CacheEngine
     * @throws CacheConfigException
     * @throws CacheException
     */
    public static function constructCacheEngine(
        IConfig $config,
        string $prefix = ''
    ): CacheEngine {

        $cacheConfig = CacheConfigBuilder::build($config);

        if ($cacheConfig instanceof FileCacheConfig) {
            return new FileCache($cacheConfig->getDirectory());
        }

        if ($cacheConfig instanceof MemcacheCacheConfig) {
            return new MemcacheCache(
                $cacheConfig->getHost(),
                $cacheConfig->getPort(),
                $prefix
            );
        }

        throw new CacheException(
            'Unable to construct CacheEngine, configuration not available'
        );
    }

    /**
     * Toggle the "enabled"-state of the Cache.
     *
     * <p>Returns the previous "enabled"-state of the Cache.</p>
     *
     * @param boolean $state
     *
     * @return boolean
     */

    final public function enable($state = true): bool
    {

        $old_state = $this->enabled;

        $this->enabled = (bool) $state;

        return $old_state;
    }

    /**
     * Spawn a CacheStore based upon the current CacheEngine.
     *
     * @param string  $store
     * @param integer $ttl
     *
     * @return CacheStore
     * @throws CacheException
     */

    final public function spawnStore($store, $ttl = 0): CacheStore
    {

        return new CacheStore($this, $store, $ttl);
    }

    /**
     * Check if the serialized value is smaller than 2 MB.
     *
     * @param mixed $value
     *
     * @return boolean
     */

    protected function validateValueSize($value): bool
    {

        // Serialize the value being saved to get a byte-stream string.
        // Strlen returns number of bytes in a string.
        $size = strlen(serialize($value));

        // The limit for values is 4 MB.
        $allowed = 1024 * 1024 * 4;
        return $size <= $allowed;
    }
}
