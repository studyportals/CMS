<?php declare(strict_types=1);

namespace StudyPortals\CMS\Module;

interface VirtualEntityDependentModule
{

    /**
     * Set the "entity" loaded for the current virtual page ID.
     *
     * @param object $Entity
     *
     * @return void
     */

    public function setVirtualEntity($Entity);
}
