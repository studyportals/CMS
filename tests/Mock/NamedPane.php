<?php declare(strict_types=1);

namespace StudyPortals\Tests\Mock;

use StudyPortals\CMS\Pane;

class NamedPane extends Pane
{

    /**
     * @return string[]
     */
    public function getModuleClasses(): array
    {
        return [
            TestTwoModule::class,
        ];
    }
}
