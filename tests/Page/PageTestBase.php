<?php declare(strict_types=1);

namespace StudyPortals\Tests\Page;

use Mockery;
use PHPUnit\Framework\TestCase;
use StudyPortals\CMS\Handlers\InputHandler;
use StudyPortals\CMS\Page\IPage;
use StudyPortals\CMS\PageTree\IPageTree;
use StudyPortals\CMS\Site\Site;
use StudyPortals\Template\Template;
use StudyPortals\Tests\Mock\PageTree;
use StudyPortals\Utils\ArrayConfig;

abstract class PageTestBase extends TestCase
{
    /**
     * @var PageTree
     */
    protected $pageTree;
    /**
     * @var Site
     */
    protected $site;
    /**
     * @var IPage
     */
    protected $page;

    /**
     * @var InputHandler
     */
    protected $inputHandler;

    public function setUp(): void
    {

        $this->site = Mockery::mock(Site::class);
        $this->site->allows()->getSiteConfig()->andReturn(Mockery::mock(IPageTree::class));
        $this->pageTree = new PageTree();
        $this->pageTree->setSite($this->site);
        $this->site->allows()->getPageTree()->andReturn($this->pageTree);
        $this->site->allows()->getBaseUrl()->andReturn(
            'https://www.example.com/'
        );
        $this->site->allows()->getHooks()->andReturn([]);
        $this->site->allows()->getConfig()->andReturn(new ArrayConfig([]));
        $this->site->allows()->getSites()->andReturn([]);
        $this->site->allows()->accessedViaProductionUrl()->andReturn(true);
        $this->site->template_path = __DIR__ . '/../Mock/';
        $this->site->framework_path = __DIR__ . '/../Mock/';
        $this->pageTree->setPageByPath('over-view/123/virtual.html');
        $this->page = $this->pageTree->getPage();
        Template::setTemplateCache('off');
        $this->inputHandler = new InputHandler();
        $this->site->allows()->getInputHandler()->andReturn($this->inputHandler);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
