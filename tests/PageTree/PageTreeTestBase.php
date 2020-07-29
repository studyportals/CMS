<?php declare(strict_types=1);

namespace StudyPortals\Tests\PageTree;

use Mockery;
use PHPUnit\Framework\TestCase;
use StudyPortals\CMS\PageTree\PageTree;
use StudyPortals\CMS\Site\Site;
use StudyPortals\Tests\Mock\PageTree as TestPageTree;

/**
 * Class PageTreeTest
 * @covers \StudyPortals\CMS\PageTree\PageTree
 * @runTestsInSeparateProcesses
 */
abstract class PageTreeTestBase extends TestCase
{
    /**
     * @var PageTree
     */
    protected $pageTree;
    /**
     * @var Mockery\LegacyMockInterface|Mockery\MockInterface|Site
     */
    protected $site;

    public function setUp(): void
    {
        $this->site = Mockery::mock(Site::class);
        $this->pageTree = new TestPageTree();
        $this->pageTree->setSite($this->site);
        $this->site->allows()->getPageTree()->andReturn($this->pageTree);
        $this->site->allows()->getBaseUrl()->andReturn('https://www.example.com/');
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
