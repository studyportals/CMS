<?php declare(strict_types=1);

namespace StudyPortals\Tests\Mock;

use StudyPortals\CMS\Pane;

class RootPane extends Pane
{

    /**
     * @return string[]
     */
    public function getModuleClasses(): array
    {
        return [
            TestModule::class,
        ];
    }
}
