<?php declare(strict_types=1);

namespace StudyPortals\Tests\Pane;

use StudyPortals\Tests\Mock\TestModule;

class PaneStructureTest extends PaneTestBase
{

    public function testFindModulesByClass(): void
    {
        $modules = $this->pane->findModulesByClass(TestModule::class);
        $this->assertCount(1, $modules);
        $this->assertInstanceOf(TestModule::class, $modules[0]);
    }

    public function testGetModuleClasses(): void
    {
        $module_classes = $this->pane->getModuleClasses();
        $this->assertCount(1, $module_classes);
        $this->assertEquals(TestModule::class, $module_classes[0]);
    }
}
