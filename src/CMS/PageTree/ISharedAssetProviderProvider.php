<?php declare(strict_types=1);

namespace StudyPortals\CMS\PageTree;

use StudyPortals\CMS\Definitions\IAssetProvider;

interface ISharedAssetProviderProvider
{

    public function getSharedAssetProvider(): IAssetProvider;
}
