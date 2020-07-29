<?php declare(strict_types=1);

namespace StudyPortals\Tests\Cache\Config;

use PHPUnit\Framework\TestCase;
use StudyPortals\Cache\CacheConfigException;
use StudyPortals\Cache\Config\File as FileCacheConfig;

class FileTest extends TestCase
{
    public function testConstructFile(): void
    {

        $cache = new FileCacheConfig('tests/Mock/Cache');

        $this->assertInstanceOf(FileCacheConfig::class, $cache);
        $this->assertEquals('./tests/Mock/Cache/', $cache->getDirectory());
    }

    public function testConstructFile_TrailingSlash(): void
    {

        $cache = new FileCacheConfig('tests/Mock/Cache/');

        $this->assertInstanceOf(FileCacheConfig::class, $cache);
        $this->assertEquals('./tests/Mock/Cache/', $cache->getDirectory());
    }

    public function testConstructFile_InvalidStore_Empty(): void
    {

        $this->expectException(CacheConfigException::class);

        new FileCacheConfig(' ');
    }

    public function testConstructFile_InvalidStore_NonExistingDirectory(): void
    {

        $this->expectException(CacheConfigException::class);

        new FileCacheConfig('__invalid__');
    }
}
