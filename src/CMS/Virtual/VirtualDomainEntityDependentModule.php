<?php declare(strict_types=1);

namespace StudyPortals\CMS\Virtual;

use StudyPortals\CMS\Module\VirtualEntityDependentModule;

interface VirtualDomainEntityDependentModule extends VirtualEntityDependentModule
{
    public function setEntity(IVirtualPageEntity $entity): void;
}
