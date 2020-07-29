<?php declare(strict_types=1);

namespace StudyPortals\Tests\Assets;

use PHPUnit\Framework\TestCase;
use StudyPortals\CMS\Definitions\IAssetProvider;
use StudyPortals\CMS\Page\Page;
use StudyPortals\CMS\Page\PageNotFoundException;
use StudyPortals\CMS\PageTree\IPageTree;
use StudyPortals\CMS\PageTree\IPageTreeNode;
use StudyPortals\Tests\Mock\PageTree;
use StudyPortals\Tests\Utility\Site;

class AssetsTest extends TestCase
{

    /**
     * @var IPageTree
     */
    private $pageTree;

    final public function setUp(): void
    {
        $this->pageTree = $this->getPageTree();
        $this->pageTree->setSite(new Site());
    }

    public function getPageTree(): IPageTree
    {
        return new PageTree();
    }

    final public function testPortalPackage(): void
    {
        $this->assertTrue(
            true,
            'Preventing "This test did not perform any assertions"'
        );
        if ($this->pageTree instanceof IAssetProvider) {
            $this->validateAssets($this->pageTree);
        }
    }

    private function validateAssets(IAssetProvider $assetProvider): void
    {
        $class = get_class($assetProvider);

        $files = array_merge(
            $assetProvider->getFooterJS(),
            $assetProvider->getHeaderCSS(),
            $assetProvider->getHeaderJS()
        );

        foreach ($files as $file) {
            $this->assertFileIsReadable(
                $file,
                "$class has an unreadable asset file '$file'"
            );
        }
    }

    /**
     * @throws PageNotFoundException
     */
    final public function testPageTree(): void
    {
        $this->assertTrue(
            true,
            'Preventing "This test did not perform any assertions"'
        );
        $allNodes = $this->pageTree->getAllNodes();
        foreach ($allNodes as $pageTreeNode) {
            $this->validatePage($pageTreeNode);
        }
    }

    /**
     * @param IPageTreeNode $pageTreeNode
     *
     * @throws PageNotFoundException
     */
    private function validatePage(IPageTreeNode $pageTreeNode): void
    {
        if ($pageTreeNode instanceof IAssetProvider) {
            $this->validateAssets($pageTreeNode);
        }

        $page = new Page($pageTreeNode);

        $modulesByClass = $page->findModulesByClass(
            IAssetProvider::class
        );
        foreach ($modulesByClass as $module) {
            $this->validateAssets($module);
        }
    }
}
