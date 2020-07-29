<?php declare(strict_types=1);

namespace StudyPortals\CMS\Definitions;

interface IAssetProvider
{
    /**
     * @return array<string>
     */
    public function getHeaderJS(): array;

    /**
     * @return array<string>
     */
    public function getHeaderCSS(): array;

    /**
     * @return array<string>
     */
    public function getFooterJS(): array;
}
