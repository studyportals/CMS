<?php declare(strict_types=1);

namespace StudyPortals\CMS\Site;

use StudyPortals\CMS\Definitions\Hooks\IHook;
use StudyPortals\CMS\Definitions\IInputHandler;
use StudyPortals\CMS\Page\IPage;
use StudyPortals\CMS\PageTree\IPageTree;
use StudyPortals\Utils\IConfig;

/**
 * @property string  $template_path
 * @property string  $framework_path
 * @property integer $page_cache_ttl
 */
interface ISite extends ICacheProvider
{

    public function getPageTree(): IPageTree;

    public function getPage(): IPage;

    public function getConfig(): IConfig;

    public function getBaseUrl(): string;

    /**
     * @return array<IHook>
     */
    public function getHooks(): array;

    public function getInputHandler(): IInputHandler;

    public function getName(): string;
}
