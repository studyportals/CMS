<?php declare(strict_types=1);

namespace StudyPortals\Tests\Mock;

use stdClass;
use StudyPortals\CMS\Module\CacheableModule;
use StudyPortals\CMS\Module\Module;
use StudyPortals\CMS\Module\VirtualEntityModule;

class TestTwoModule extends Module implements VirtualEntityModule, ITestModule, CacheableModule
{

    public function displayMain()
    {
        return 'Hello World!';
    }

    /**
     * @param string $id
     * @suppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setVirtualPageID($id): void
    {
        // TODO: Implement setVirtualPageID() method.
    }

    public function getVirtualPageName(): string
    {
        return 'virtual';
    }

    public function getVirtualEntity()
    {
        return new stdClass();
    }

    /**
     * @inheritDoc
     */
    public function allowCaching(): bool
    {
        return true;
    }

    public function getTTL(): int
    {
        return 123;
    }
}
