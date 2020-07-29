<?php declare(strict_types=1);

namespace StudyPortals\Tests\Page;

use StudyPortals\Template\NodeNotFoundException;
use StudyPortals\Template\Template;
use StudyPortals\Template\TemplateException;
use StudyPortals\Tests\Mock\OverviewPage;
use StudyPortals\Tests\Mock\PageTree;
use StudyPortals\Tests\Mock\VirtualPage;

class PageRoutingTest extends PageTestBase
{

    public function testSetSubtitle(): void
    {
        $this->page->setSubtitle('mock', true);
        $this->assertEquals('Virtual page mock', $this->page->getSubtitle());
    }

    public function testFindPath(): void
    {
        $path = $this->page->findPath();
        $this->assertCount(1, $path);
        $this->assertInstanceOf(OverviewPage::class, $path[0]);
    }

    public function testSetVirtualID(): void
    {
        $this->page->setVirtualID('42');
        $this->assertEquals(42, $this->page->getVirtualId());
    }

    /**
     * @throws NodeNotFoundException
     */
    public function testSetPageNotIndexable(): void
    {
        $this->page->setPageNotIndexable(true);
        $template = $this->page->display();
        $this->assertStringContainsString(
            '<meta name="robots" content="noindex">',
            (string) $template->getChildByName('Header')
        );
    }

    /**
     * @throws TemplateException
     */
    public function testSetBaseUrlTemplateVariables(): void
    {
        $baseUrl = 'https://www.example.com/';
        Template::setDefaultVariable('base_url', $baseUrl);
        $template = Template::create(
            __DIR__ . '/../Mock/Resources/Template.tp4'
        );
        $this->assertEquals($baseUrl, $template->getValue('base_url'));
    }

    public function testGetPageTree(): void
    {
        $this->assertInstanceOf(PageTree::class, $this->page->getPageTree());
    }

    /**
     * @throws NodeNotFoundException
     * @throws TemplateException
     */
    public function testAddHeaderInclude(): void
    {
        $this->page->addHeaderInclude(
            'https://www.example.com/test/Mock/Resources/Javascript.js'
        );
        $template = $this->page->display();
        $this->assertStringContainsString(
            'src="https://www.example.com/test/Mock/Resources/Javascript.js"',
            (string) $template->getChildByName('Header')
        );
    }

    public function testSetDescription(): void
    {
        $description = 'It\'s a me! Mario!';
        $this->page->setDescription($description);
        $this->assertEquals($description, $this->page->getDescription());
    }

    public function testGetSite(): void
    {
        $this->assertEquals($this->site, $this->page->getSite());
    }

    public function testGetURL(): void
    {
        $this->assertEquals(
            'https://www.example.com/over-view/123/virtual.html',
            $this->page->getURL(true)
        );
        $this->assertEquals(
            'over-view/123/virtual.html',
            $this->page->getURL()
        );
    }

    public function testGetVirtualName(): void
    {
        $this->assertEquals('virtual', $this->page->getVirtualName());
    }

    public function testGetPageTreeNode(): void
    {
        $this->assertInstanceOf(
            VirtualPage::class,
            $this->page->getPageTreeNode()
        );
    }

    /**
     * @throws TemplateException
     */
    public function testPopulateCommonFields(): void
    {

        $template = Template::create(__DIR__ . '/../Mock/Resources/Template.tp4');
        $this->page->populateCommonFields($template);

        $properties = [
            'title' => $this->page->getPageTreeNode()->getRoute(),
            'subtitle' => $this->page->getSubtitle(),
            'description' => $this->page->getDescription(),
            'structuredMarkupData' => $this->page->getStructuredMarkupData(),
            'page_url' => $this->page->getFullURL(),
            'canonical_url' => $this->page->getCanonicalURL(),
            'framework_path' => $this->page->getSite()->framework_path,
            'template_path' => $this->page->getSite()->template_path .
                               dirname($this->page->getPageTreeNode()->getTemplatePath()) .
                               '/',
            'debug_mode' => defined('DEBUG_MODE') && DEBUG_MODE,
            'virtual_path' => $this->page->getPageTreeNode()->getURL(),
            'virtual_id' => $this->page->getVirtualId(),
            'base_url' => $this->page->getSite()->getBaseUrl(),
        ];

        foreach ($properties as $name => $value) {
            $this->assertEquals(
                $value,
                $template->getValue($name),
                (string) ($name)
            );
        }
    }
}
