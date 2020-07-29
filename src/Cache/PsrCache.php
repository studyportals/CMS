<?php declare(strict_types=1);

namespace StudyPortals\Cache;

use DateInterval;
use LogicException;
use Psr\SimpleCache\CacheInterface;

/**
 * @SuppressWarnings(PHPMD)
 * @codeCoverageIgnore
 */
class PsrCache implements CacheInterface
{
    private $Cache;

    /**
     * @param CacheStore $Cache
     * @return void
     */

    public function __construct(CacheStore $Cache)
    {

        $this->Cache = $Cache;
    }

    /**
     * @param string $key
     * @param mixed  $default
     * @return mixed
     * @throws PsrInvalidArgumentException
     * @throws PsrCacheException
     */
    public function get($key, $default = null)
    {

        $error = false;

        try {
            $result = $this->Cache->get($key, $error);
        } catch (CacheException $e) {
            throw new PsrCacheException($e->getMessage(), $e->getCode(), $e);
        }

        if ($error === true) {
            if ($result === null) {
                throw new PsrInvalidArgumentException(
                    "Key '{$key}' is invalid"
                );
            }

            return $default;
        }

        return $result;
    }

    /**
     * @param string                $key
     * @param mixed                 $value
     * @param null|int|DateInterval $ttl
     * @return bool
     * @throws LogicException
     * @throws PsrCacheException
     */
    public function set($key, $value, $ttl = null): bool
    {

        if ($ttl instanceof DateInterval) {
            throw new LogicException(
                'PsrCache::set() with DateInterval $ttl is not implemented'
            );
        }

        try {
            return $this->Cache->set($key, $value, (int) $ttl);
        } catch (CacheException $e) {
            throw new PsrCacheException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $key
     * @return bool
     * @throws PsrCacheException
     */
    public function delete($key): bool
    {

        try {
            return $this->Cache->delete($key);
        } catch (CacheException $e) {
            throw new PsrCacheException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     *
     * @return bool
     * @throws LogicException
     */

    public function clear(): bool
    {

        throw new LogicException(
            'PsrCache::clear() is not implemented'
        );
    }

    /**
     *
     * @param iterable<string> $keys
     * @param mixed|null $default
     * @return iterable<mixed>
     * @throws LogicException
     */

    public function getMultiple($keys, $default = null)
    {

        throw new LogicException(
            'PsrCache::getMultiple() is not implemented'
        );
    }

    /**
     *
     * @param iterable<mixed> $values
     * @param null|int|DateInterval $ttl
     * @return bool
     * @throws LogicException
     */

    public function setMultiple($values, $ttl = null): bool
    {

        throw new LogicException(
            'PsrCache::setMultiple() is not implemented'
        );
    }

    /**
     *
     * @param iterable<string> $keys
     * @return bool
     * @throws LogicException
     */

    public function deleteMultiple($keys): bool
    {

        throw new LogicException(
            'PsrCache::deleteMultiple() is not implemented'
        );
    }

    /**
     *
     * @param string $key
     * @return bool
     * @throws LogicException
     */

    public function has($key): bool
    {

        throw new LogicException('PsrCache::has() is not implemented');
    }
}
