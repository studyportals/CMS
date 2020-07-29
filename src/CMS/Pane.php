<?php declare(strict_types=1);

namespace StudyPortals\CMS;

use StudyPortals\CMS\ModuleCollection\IModuleCollection;
use StudyPortals\CMS\ModuleCollection\ModuleCollection;

abstract class Pane extends ModuleCollection
{

    public function __construct(
        IModuleCollection $parent,
        string $name
    ) {
        parent::__construct($parent, "Pane_{$name}", $this->getModuleClasses());
    }

    /**
     * @return array<string>
     */
    abstract public function getModuleClasses(): array;
}
