<?php declare(strict_types=1);

namespace StudyPortals\Tests\PageTree;

use StudyPortals\CMS\Page\PageNotSetException;
use StudyPortals\Tests\Mock\ErrorPage;

class PageTreeStructureTest extends PageTreeTestBase
{
    public function testGetIterator(): void
    {
        $this->assertIsIterable($this->pageTree->getIterator());
    }

    public function testGetPage(): void
    {
        $this->expectException(PageNotSetException::class);
        $this->pageTree->getPage();
    }

    public function testGetSite(): void
    {
        $site = $this->pageTree->getSite();
        $this->assertEquals($this->site, $site);
    }

    public function testGetErrorPage(): void
    {
        $errorPage = $this->pageTree->getErrorPage();
        $this->assertInstanceOf(ErrorPage::class, $errorPage);
    }

    public function testSetErrorPage(): void
    {
        $this->pageTree->setErrorPage();
        $Page = $this->pageTree->getPage();
        $pageTreeNode = $Page->getPageTreeNode();
        $this->assertInstanceOf(ErrorPage::class, $pageTreeNode);
    }

    public function testSetDefaultPages(): void
    {
        $errorPage = $this->pageTree->getErrorPage();
        $this->assertInstanceOf(ErrorPage::class, $errorPage);
    }

    public function testGetPageTreeNodeClasses(): void
    {
        $pageTreeNodeClasses = $this->pageTree->getPageTreeNodeClasses();
        $this->assertIsArray($pageTreeNodeClasses);
    }

    public function testFindNodeByPath(): void
    {
        $node = $this->pageTree->getErrorPage();
        $this->assertInstanceOf(ErrorPage::class, $node);
    }
}
