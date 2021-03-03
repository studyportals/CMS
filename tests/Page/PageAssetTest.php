<?php declare(strict_types=1);

namespace StudyPortals\Tests\Page;

use StudyPortals\CMS\ResourceNotFoundException;
use StudyPortals\Template\NodeNotFoundException;
use StudyPortals\Tests\Mock\ITestModule;

class PageAssetTest extends PageTestBase
{
    public function testGetFullURL(): void
    {
        $this->assertEquals(
            'https://www.example.com/over-view/123/virtual.html',
            $this->page->getFullUrl()
        );
    }

    /**
     * @throws NodeNotFoundException
     */
    public function testAddFooterInclude(): void
    {
        $this->page->addFooterInclude(
            'https://www.example.com/test/Mock/Resources/Javascript.js'
        );
        $template = $this->page->display();
        $this->assertEquals(
            '<script ' .
            'src="https://www.example.com/test/Mock/Resources/Javascript.js">' .
            '</script>',
            (string) ($template->getChildByName('Footer'))
        );
    }

    public function testGetIterator(): void
    {
        $this->assertIsIterable($this->page->getIterator());
    }

    public function testGetSubtitle(): void
    {
        $this->assertEquals('Virtual page', $this->page->getSubtitle());
    }

    public function testSetCanonicalURL(): void
    {
        $this->page->setCanonicalURL('https://www.example.invalid/');
        $this->assertEquals(
            'https://www.example.invalid/',
            $this->page->getCanonicalURL()
        );
    }

    public function testSetStructuredMarkupData(): void
    {
        $this->page->setStructuredMarkupData('Structured!');
        $template = $this->page->display();
        $this->assertEquals(
            'Structured!',
            $template->getValue('structuredMarkupData')
        );
    }

    public function testAddCanonicalParameters(): void
    {
        $this->inputHandler->set(INPUT_GET, 'fake', 'news');
        $this->inputHandler->set(INPUT_GET, 'paaaarame', 'news');
        $this->page->setCanonicalURL(
            'https://www.example.com/combination/123/virtual.html'
        );
        $this->page->addCanonicalParameters(['FaKe']);
        $canonicalURL = $this->page->getCanonicalURL();
        $this->assertEquals(
            'https://www.example.com/combination/123/virtual.html?fake=news',
            $canonicalURL
        );
        $this->page->addCanonicalParameters(['Paaaarame']);
        $this->assertEquals(
            'https://www.example.com/combination/123/virtual.html?fake=news&paaaarame=news',
            $this->page->getCanonicalURL()
        );
        $this->inputHandler->remove(INPUT_GET, 'fake');
        $this->inputHandler->remove(INPUT_GET, 'paaaarame');
    }

    public function testIsValidAssetHttp(): void
    {
        $url = 'https://www.example.com/';
        $this->assertTrue($this->page::isValidAsset($url));
    }

    public function testIsValidAssetLocal(): void
    {
        $file = __DIR__ . '/../Mock/Resources/Javascript.js';
        $this->assertTrue($this->page::isValidAsset($file));
    }

    public function testIsInValidAsset(): void
    {
        $file = 'invalid';
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            $this->expectException(ResourceNotFoundException::class);
        }
        $this->assertFalse($this->page::isValidAsset($file));
    }

    public function testFindModulesByClass(): void
    {
        $modules = $this->page->findModulesByClass(ITestModule::class);
        $this->assertCount(2, $modules);
    }

    public function testGetTTL(): void
    {
        $this->assertEquals(123, $this->page->getTTL());
    }

    public function testGetDescription(): void
    {
        $this->assertEquals('Virtual', $this->page->getDescription());
    }
}
