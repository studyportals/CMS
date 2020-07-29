<?php declare(strict_types=1);

namespace StudyPortals\Tests\PageTree;

use StudyPortals\CMS\Page\PageNotFoundException;
use StudyPortals\CMS\Site\InvalidURLException;
use StudyPortals\CMS\ResourceNotFoundException;
use StudyPortals\Tests\Mock\FinalPage;
use StudyPortals\Tests\Mock\PageTree as TestPageTree;
use StudyPortals\Tests\Mock\VirtualPage;
use Throwable;

class PageTreeRoutingTest extends PageTreeTestBase
{

    /**
     * @throws InvalidURLException
     * @throws PageNotFoundException
     * @throws ResourceNotFoundException
     */
    public function testAll(): void
    {
        $this->pageTree->setPageByPath(
            'combination/over-view/final/more/can/12/virtual.html'
        );
        $this->assertInstanceOf(
            FinalPage::class,
            $this->pageTree->getPage()->getPageTreeNode()
        );
    }

    /**
     * @throws InvalidURLException
     * @throws PageNotFoundException
     * @throws ResourceNotFoundException
     */
    public function testSetPageByPathMissing(): void
    {
        $this->expectException(PageNotFoundException::class);
        $this->pageTree->setPageByPath('missing/');
    }

    /**
     * @throws InvalidURLException
     * @throws PageNotFoundException
     * @throws ResourceNotFoundException
     */
    public function testSetPageByPath(): void
    {
        $this->pageTree->setPageByPath('combination/');
        $Page = $this->pageTree->getPage();
        $pageTree = $Page->getPageTree();
        $this->assertEquals($this->pageTree, $pageTree);
    }

    /**
     * @throws InvalidURLException
     * @throws PageNotFoundException
     * @throws ResourceNotFoundException
     */
    public function testVirtualPage(): void
    {
        $this->pageTree->setPageByPath('over-view/1/virtual.html');
        $this->assertInstanceOf(
            VirtualPage::class,
            $this->pageTree->getPage()->getPageTreeNode()
        );
    }

    /**
     * @throws PageNotFoundException
     */
    public function testVirtualPage_Redirect(): void
    {
        try {
            $this->pageTree->setPageByPath('over-view/1/vir');
            $this->fail('No InvalidUrlException thrown');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidURLException::class, $e);
            $this->assertSame(
                'https://www.example.com/over-view/1/virtual.html',
                $e->getCorrectURL()
            );
        }
        try {
            $this->pageTree->setPageByPath('over-view/1/virtual');
            $this->fail('No InvalidUrlException thrown');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidURLException::class, $e);
            $this->assertSame(
                'https://www.example.com/over-view/1/virtual.html',
                $e->getCorrectURL()
            );
        }
        try {
            $this->pageTree->setPageByPath('over-view/1/');
            $this->fail('No InvalidUrlException thrown');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidURLException::class, $e);
            $this->assertSame(
                'https://www.example.com/over-view/1/virtual.html',
                $e->getCorrectURL()
            );
        }
        try {
            $this->pageTree->setPageByPath('over-view/1');
            $this->fail('No InvalidUrlException thrown');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidURLException::class, $e);
            $this->assertSame(
                'https://www.example.com/over-view/1/virtual.html',
                $e->getCorrectURL()
            );
        }
    }

    /**
     * @throws InvalidURLException
     * @throws PageNotFoundException
     * @throws ResourceNotFoundException
     */
    public function testFinalPage(): void
    {
        $this->pageTree->setPageByPath('final/more/can/');
        $this->assertInstanceOf(
            FinalPage::class,
            $this->pageTree->getPage()->getPageTreeNode()
        );
    }

    /**
     * @throws InvalidURLException
     * @throws PageNotFoundException
     * @throws ResourceNotFoundException
     */
    public function testGetHomePage(): void
    {
        $this->pageTree->setPageByPath('/');
        $homePage = $this->pageTree->getPage()->getPageTreeNode();
        $this->assertInstanceOf(TestPageTree::class, $homePage);

        $this->assertEquals('/', $homePage->getURL());
    }
}
