<?php declare(strict_types=1);

namespace StudyPortals\Cache;

use StudyPortals\CMS\ExceptionHandler;

/**
 * Memcache (PECL/Memcache) based caching-engine.
 *
 * <p>This engine forms a wrapper around PHP/PECL's Memcache class.</p>
 *
 * @SuppressWarnings(PHPMD)
 * @codeCoverageIgnore
 */
class MemcacheCache extends CacheEngine
{

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var \Memcache
     */
    protected $Memcache;

    /**
     * Construct a new MemcacheCache.
     *
     * @param string  $host
     * @param integer $port
     * @param string  $prefix
     *
     * @throws CacheException
     */

    public function __construct($host, $port = 11211, $prefix = '')
    {

        $this->host = (string) $host;
        $this->port = (int) $port;

        $preg_match = preg_match('/^[a-z0-9]*$/', $prefix);
        if ($prefix !== '' && $preg_match !== 1) {
            throw new CacheException(
                "Invalid prefix '$prefix': needs to be alphanumeric"
            );
        }

        $this->prefix = (string) $prefix;

        $this->Memcache = new \Memcache();

        if (!Memcache::connect($this->Memcache, $this->host, $this->port)) {
            throw new CacheException('Failed to connect to Memcache');
        }
    }

    /**
     * Add an entry to the MemcacheCache.
     *
     * @param string        $name
     * @param mixed         $value
     * @param integer|float $ttl
     *
     * @return boolean
     * @throws CacheException
     */

    public function set($name, $value, $ttl = 0): bool
    {

        $name = trim($name);

        if ($name === '') {
            throw new CacheException('Cache-entry name cannot be empty');
        }

        $name = $this->prependPrefix($name);

        if (is_resource($value)) {
            throw new CacheException('Cannot cache values of type "resource"');
        }

        if (!$this->validateValueSize($value)) {
            ExceptionHandler::notice(
                "Value in $name is too big to be stored in memcache."
            );
        }

        // A 30 day+ TTL is interpreted as a timestamp by memcached

        if ($ttl > 2592000) {
            $ttl = time() + $ttl;
        }

        // Name cannot be longer than 250 characters

        assert('strlen($name) <= 250');
        if (strlen($name) > 250) {
            return false;
        }

        $result = $this->Memcache->set($name, $value, 0, (int) $ttl);

        if ($result === false) {
            throw new CacheException(
                "Failed to set '$name'"
            );
        }

        return $result;
    }

    /**
     * Retrieve an entry from the MemcacheCache.
     *
     * @param string  $name
     * @param boolean $error
     *
     * @return mixed
     * @see Cache::get()
     * @see Memcache::get()
     */

    public function get($name, &$error = false)
    {

        // If disabled, return nothing so that this cache is overwritten

        if (!$this->enabled) {
            return null;
        }

        $name = $this->prependPrefix($name);

        error_clear_last();

        $value = @$this->Memcache->get($name);

        // Attempt to separate "real" errors from missing cache entries

        $last_error = error_get_last();

        if (
            $last_error !== null
            && $last_error['type'] === E_WARNING
        ) {
            $error = true;

            return null;
        }

        return $value;
    }

    /**
     * Delete an entry from the MemcacheCache.
     *
     * @param string $name
     *
     * @return boolean
     * @see Cache::delete()
     * @see Memcache::delete()
     */

    public function delete($name): bool
    {

        $name = $this->prependPrefix($name);

        return @$this->Memcache->delete($name);
    }

    /**
     * String representation of object
     * @link  http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */

    public function serialize(): string
    {

        return serialize("$this->host:$this->port");
    }

    /**
     * Constructs the object
     * @link  http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @throws CacheException
     * @since 5.1.0
     */

    public function unserialize($serialized): void
    {

        [
            $this->host,
            $this->port,
        ] = explode(':', unserialize($serialized));

        $this->Memcache = new \Memcache();

        if (!Memcache::connect($this->Memcache, $this->host, $this->port)) {
            throw new CacheException('Failed to connect to Memcache');
        }
    }

    private function prependPrefix(string $name): string
    {

        if ($this->prefix !== '') {
            $name = $this->prefix . ':' . $name;
        }

        return $name;
    }
}
