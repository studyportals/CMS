<?php declare(strict_types=1);

namespace StudyPortals\CMS\Virtual;

use StudyPortals\CMS\Module\VirtualEntityModule;

interface VirtualDomainEntityModule extends VirtualEntityModule
{

    public function getEntity(): IVirtualPageEntity;
}
