<?php declare(strict_types=1);

namespace StudyPortals\Tests\Cache\Config;

use PHPUnit\Framework\TestCase;
use StudyPortals\Cache\CacheConfigException;
use StudyPortals\Cache\Config\Memcache as MemcacheCacheConfig;

class MemcacheTest extends TestCase
{
    public function testConstructMemcache(): void
    {

        $cache = new MemcacheCacheConfig('127.0.0.1:11211');

        $this->assertInstanceOf(MemcacheCacheConfig::class, $cache);
        $this->assertEquals('127.0.0.1', $cache->getHost());
        $this->assertEquals(11211, $cache->getPort());
    }

    public function testConstructMemcache_InvalidStore(): void
    {

        $this->expectException(CacheConfigException::class);

        new MemcacheCacheConfig('__invalid__');
    }

    public function testConstructMemcache_InvalidHost(): void
    {

        $this->expectException(CacheConfigException::class);

        new MemcacheCacheConfig(' :11211');
    }

    public function testConstructMemcache_InvalidPort_NotInteger(): void
    {

        $this->expectException(CacheConfigException::class);

        new MemcacheCacheConfig('127.0.0.1:__invalid__');
    }

    public function testConstructMemcache_InvalidPort_OutOfRange_Low(): void
    {

        $this->expectException(CacheConfigException::class);

        new MemcacheCacheConfig('127.0.0.1:0');
    }

    public function testConstructMemcache_InvalidPort_OutOfRange_High(): void
    {

        $this->expectException(CacheConfigException::class);

        new MemcacheCacheConfig('127.0.0.1:65536');
    }
}
