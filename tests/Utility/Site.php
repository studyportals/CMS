<?php declare(strict_types=1);

namespace StudyPortals\Tests\Utility;

use StudyPortals\Cache\CacheEngine;
use StudyPortals\CMS\Definitions\IInputHandler;
use StudyPortals\CMS\Page\IPage;
use StudyPortals\CMS\PageTree\IPageTree;
use StudyPortals\CMS\Site\ICacheBuilder;
use StudyPortals\CMS\Site\ISite;
use StudyPortals\Utils\IConfig;

class Site implements ISite
{
    /**
     * @var CacheEngine
     */
    private $cache;

    public function getPageTree(): IPageTree
    {
        return \Mockery::mock(IPageTree::class);
    }

    public function getPage(): IPage
    {
        return \Mockery::mock(IPage::class);
    }

    public function getConfig(): IConfig
    {
        return \Mockery::mock(IConfig::class);
    }

    public function getBaseUrl(): string
    {
        return 'base.url';
    }

    public function getHooks(): array
    {
        return [];
    }

    public function getInputHandler(): IInputHandler
    {
        return \Mockery::mock(IInputHandler::class);
    }

    public function getCache(): CacheEngine
    {
        return \Mockery::mock(CacheEngine::class);
    }

    public function getName(): string
    {
        return 'mock';
    }

    public function enableCache(): void
    {
    }

    public function disableCache(): void
    {
    }

    public function isCacheEnabled(): bool
    {
        return false;
    }

    public function buildCache(ICacheBuilder $builder): void
    {
        $this->cache = $builder->build(\Mockery::mock(IConfig::class));
    }
}
