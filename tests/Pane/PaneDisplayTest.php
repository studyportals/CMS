<?php declare(strict_types=1);

namespace StudyPortals\Tests\Pane;

use StudyPortals\Template\NodeNotFoundException;
use StudyPortals\Template\Template;
use StudyPortals\Template\TemplateException;

class PaneDisplayTest extends PaneTestBase
{

    public function testGetPage(): void
    {
        $page = $this->pane->getPage();
        $this->assertEquals($this->page, $page);
    }

    /**
     * @throws NodeNotFoundException
     * @throws TemplateException
     */
    public function testPopulateTemplate(): void
    {

        $this->assertTrue(true);
        $template = Template::create(__DIR__ . '/../Mock/Resources/Template.tp4');

        $Body = $template->getChildByName('Body');
        $this->pane->populateTemplate($Body);

        $this->assertEquals(
            '<div class="Module StudyPortals_Tests_Mock_TestModule" data-module=""> Hello World! </div>',
            trim((string) $Body->getChildByName('Pane_base'))
        );
    }
    public function testGetIterator(): void
    {
        $this->assertIsIterable($this->pane->getIterator());
    }

    public function testGetName(): void
    {
        $this->assertEquals('Pane_base', $this->pane->getName());
    }
}
