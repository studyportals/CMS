<?php declare(strict_types=1);

namespace StudyPortals\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use StudyPortals\CMS\Page\Page;
use StudyPortals\CMS\PageTree\IPageTree;
use StudyPortals\CMS\PageTree\IPageTreeNode;
use StudyPortals\CMS\Site\Site;
use StudyPortals\Tests\Mock\CombinationPage;
use StudyPortals\Tests\Mock\PageTree;

/**
 * Class PageTreeNodeTest
 * @covers \StudyPortals\CMS\PageTree\PageTreeNode
 * @runTestsInSeparateProcesses
 */
class PageTreeNodeTest extends TestCase
{
    /**
     * @var IPageTree
     */
    protected $pageTree;
    /**
     * @var Mockery\LegacyMockInterface|Mockery\MockInterface|Site
     */
    protected $site;
    /**
     * @var IPageTreeNode
     */
    protected $pageTreeNode;

    public function setUp(): void
    {
        $this->site = Mockery::mock(Site::class);
        $this->site->allows()->getName()->andReturn('MockSite');
        $this->pageTree = new PageTree();
        $this->pageTree->setSite($this->site);
        $this->site->allows()->getPageTree()->andReturn($this->pageTree);
        $this->site->allows()->getBaseUrl()->andReturn('https://www.example.com/');
        $this->pageTreeNode = $this->pageTree->findNodeByPath('combination/over-view');
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testGetParent(): void
    {

        $parent = $this->pageTreeNode->getParent();
        $this->assertInstanceOf(CombinationPage::class, $parent);
    }

    public function testGetName(): void
    {

        $this->assertEquals('over-view', $this->pageTreeNode->getRoute());
    }

    public function testGetFullURL(): void
    {

        $this->assertEquals('https://www.example.com/combination/over-view/', $this->pageTreeNode->getFullURL());
    }

    public function testGetCommonName(): void
    {

        $this->assertEquals('over-view', $this->pageTreeNode->getRoute());
    }

    public function testGetURL(): void
    {

        $this->assertEquals('combination/over-view/', $this->pageTreeNode->getURL());
    }

    public function testGetPageTree(): void
    {

        $this->assertInstanceOf(PageTree::class, $this->pageTreeNode->getPageTree());
    }

    public function testLoadPanes(): void
    {

        $this->assertCount(
            2,
            $this->pageTreeNode->constructModuleCollection(Mockery::mock(Page::class))
        );
    }
}
