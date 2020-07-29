<?php declare(strict_types=1);

namespace StudyPortals\Cache;

/**
 * @SuppressWarnings(PHPMD)
 * @codeCoverageIgnore
 */
abstract class Memcache
{

    public const COMPRESS_THRESHOLD    = 20000;
    public const COMPRESS_SAVINGS      = 0.2;

    /**
     * Connect a Memcache-instance.
     *
     * <p>The connection-state of the provided {@link $Memcache}-instance is
     * <strong>not</strong> checked before attempting to connect it. So, passing
     * in an already connected instance is not recommended and will most likely
     * lead to unexpected results.</p>
     *
     * @param \Memcache $Memcache
     * @param string $host
     * @param integer $port
     * @param boolean $persistent
     * @return boolean
     */

    public static function connect(
        \Memcache $Memcache,
        $host,
        $port = 11211,
        $persistent = true
    ): bool {

        $host = (string) $host;
        $port = (int) $port;

        if ($persistent) {
            $result = @$Memcache->pconnect($host, $port);
        } else {
            $result = @$Memcache->connect($host, $port);
        }

        if ($result) {

            /** @noinspection PhpUnusedLocalVariableInspection */
            $compress = @$Memcache->setcompressthreshold(
                self::COMPRESS_THRESHOLD,
                self::COMPRESS_SAVINGS
            );

            assert('$compress !== false');
        }

        return $result;
    }
}
