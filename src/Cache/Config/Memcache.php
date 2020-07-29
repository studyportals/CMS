<?php declare(strict_types=1);

namespace StudyPortals\Cache\Config;

use StudyPortals\Cache\CacheConfig;
use StudyPortals\Cache\CacheConfigException;

class Memcache implements CacheConfig
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
     * Memcache constructor.
     *
     * @param string $store
     *
     * @throws CacheConfigException
     */
    public function __construct(string $store)
    {

        [$host, $port] = explode(':', $store, 2);

        $this->host = trim($host);
        $this->port = (int) trim((string) $port);

        if (empty($this->host)) {
            throw new CacheConfigException(
                "Invalid memcache-host '{$this->host}' provided"
            );
        }

        if ($this->port <= 0 || $this->port > 65535) {
            throw new CacheConfigException(
                "Invalid memcache-port '{$this->port}' provided"
            );
        }
    }

    public function getHost(): string
    {

        return $this->host;
    }

    public function getPort(): int
    {

        return $this->port;
    }
}
