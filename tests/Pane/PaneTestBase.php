<?php declare(strict_types=1);

namespace StudyPortals\Tests\Pane;

use Mockery;
use PHPUnit\Framework\TestCase;
use StudyPortals\CMS\Page\IPage;
use StudyPortals\CMS\Site\Site;
use StudyPortals\CMS\ResourceNotFoundException;
use StudyPortals\Tests\Mock\PageTree;
use StudyPortals\Tests\Mock\RootPane;

/**
 * Class PaneTest
 * @covers \StudyPortals\CMS\Pane
 * @runTestsInSeparateProcesses
 */
abstract class PaneTestBase extends TestCase
{
    /**
     * @var PageTree
     */
    protected $pageTree;
    /**
     * @var Mockery\LegacyMockInterface|Mockery\MockInterface|Site
     */
    protected $site;
    /**
     * @var IPage
     */
    protected $page;
    /**
     * @var RootPane
     */
    protected $pane;

    /**
     * @throws ResourceNotFoundException
     */
    public function setUp(): void
    {
        $this->site = Mockery::mock(Site::class);
        $this->site->allows()->getName()->andReturn('MockSite');
        $this->pageTree = new PageTree();
        $this->site->allows()->getPageTree()->andReturn($this->pageTree);
        $this->site->allows()->getBaseUrl()->andReturn('https://www.example.com/');
        $this->pageTree->setPageByPath('combination/over-view/');
        $this->page = $this->pageTree->getPage();
        $this->pane = new RootPane($this->page, 'base');
        $this->pane->load();
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
