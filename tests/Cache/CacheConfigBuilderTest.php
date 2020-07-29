<?php declare(strict_types=1);

namespace StudyPortals\Tests\Cache;

use PHPUnit\Framework\TestCase;
use StudyPortals\Cache\CacheConfigBuilder;
use StudyPortals\Cache\CacheConfigException;
use StudyPortals\Cache\Config\File as FileCacheConfig;
use StudyPortals\Cache\Config\Memcache as MemcacheCacheConfig;
use StudyPortals\Utils\ArrayConfig;

class CacheConfigBuilderTest extends TestCase
{
    /**
     * @throws CacheConfigException
     */
    public function testBuild_MemcacheCacheConfig(): void
    {

        $config = new ArrayConfig(
            [
                'CACHE_ENGINE' => 'Memcache',
                'CACHE_STORE'  => '127.0.0.1:11211',
            ]
        );

        $cache = CacheConfigBuilder::build($config);

        $this->assertInstanceOf(MemcacheCacheConfig::class, $cache);
    }

    /**
     * @throws CacheConfigException
     */
    public function testBuild_FileCacheConfig(): void
    {

        $config = new ArrayConfig(
            [
                'CACHE_ENGINE' => 'File',
                'CACHE_STORE'  => '.',
            ]
        );

        $cache = CacheConfigBuilder::build($config);

        $this->assertInstanceOf(FileCacheConfig::class, $cache);
    }
    public function testBuild_InvalidEngine(): void
    {

        $config = new ArrayConfig(
            ['CACHE_ENGINE' => '__invalid__']
        );

        $this->expectException(CacheConfigException::class);

        CacheConfigBuilder::build($config);
    }
}
