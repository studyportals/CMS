<?php declare(strict_types=1);

namespace StudyPortals\Cache;

/**
 * Cache Store.
 *
 * <p>The CacheStore provides some syntactic sugar around the CacheEngine. It
 * provides for automatic "prefixing" of entries in the cache as to provide an
 * easy way of preventing accidental naming collisions if many items from
 * many different places are stored in the same cache.</p>
 *
 * @SuppressWarnings(PHPMD)
 * @codeCoverageIgnore
 */
class CacheStore implements Cache
{

    /**
     * @var string
     */
    protected $store;

    /**
     * @var int|null
     */
    protected $ttl;

    /**
     * @var CacheEngine
     */
    protected $Cache;

    /**
     * Construct a new CacheStore.
     *
     * <p>The {@link $store} argument is used as prefix for all values stored
     * from this store. A colon is used to separate the prefix from the entry
     * names and as such cannot be used in either.</p>
     *
     * <p>The optional {@link $ttl} argument enables you to set a global
     * time-to-live for the entire store. If no TTL is provided when calling
     * CacheStore::set() the global value (if set) is used. If no global TTL
     * is set, omitting the TTL argument for CacheStore::set() will result in
     * the entry never expiring.</p>
     *
     * @param CacheEngine $Cache
     * @param string      $store
     * @param integer     $ttl
     *
     * @throws CacheException
     */

    public function __construct(CacheEngine $Cache, $store, $ttl = null)
    {

        $this->store = $this->cleanName($store);

        if ($ttl !== null) {
            $this->ttl = (int) $ttl;
        }

        $this->Cache = $Cache;
    }

    /**
     * Clean a name for use within the CacheStore.
     *
     * <p>This method prepares a string for use as a name (for prefix or
     * entity) within the CacheStore. Such a name cannot contain colons nor is
     * it allowed to be empty.</p>
     *
     * @param string $name
     *
     * @return string
     * @throws CacheException
     */

    protected function cleanName($name): string
    {

        if (strpos($name, ':') !== false) {
            [$name,] = explode(':', $name);
        }
        $name = trim($name);

        if ($name === '') {
            throw new CacheException('Cache-entry name cannot be empty');
        }

        return strtolower($name);
    }

    /**
     * Add an entry to the CacheStore.
     *
     * <p>The cache entry's {@link $name} parameter is treated in a case
     * <em>insensitive</em> manner!</p>
     *
     * <p>The {@link $ttl} value is optional, when omitted the global value
     * specified for this CacheStore will be used. When this value is also
     * omitted, the entry will "never" expire.</p>
     *
     * @param string       $name
     * @param mixed        $value
     * @param integer|null $ttl
     *
     * @return boolean
     * @throws CacheException
     * @see Cache::set()
     */

    public function set($name, $value, $ttl = 0): bool
    {

        $name = $this->cleanName($name);

        if ($ttl === 0 && $this->ttl !== null) {
            $ttl = $this->ttl;
        }

        return $this->Cache->set("{$this->store}:$name", $value, (int) $ttl);
    }

    /**
     * Get an entry from the CacheStore.
     *
     * @param string  $name
     * @param boolean $error
     *
     * @return mixed
     * @see Cache::get()
     */

    public function get($name, &$error = false)
    {

        try {
            $name = $this->cleanName($name);
        } catch (CacheException $e) {
            $error = true;

            return null;
        }

        return $this->Cache->get("{$this->store}:$name", $error);
    }

    /**
     * Delete an entry from the CacheStore.
     *
     * @param string $name
     *
     * @return boolean
     * @see Cache::delete()
     */

    public function delete($name): bool
    {

        try {
            $name = $this->cleanName($name);
        } catch (CacheException $e) {
            return false;
        }

        return $this->Cache->delete("{$this->store}:$name");
    }
}
