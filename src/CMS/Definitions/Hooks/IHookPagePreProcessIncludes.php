<?php declare(strict_types=1);

namespace StudyPortals\CMS\Definitions\Hooks;

use StudyPortals\CMS\Page\IPage;
use StudyPortals\Template\Template;

interface IHookPagePreProcessIncludes extends IHook
{
    /**
     * @param IPage    $page
     * @param Template $template
     */
    public function handlePagePreProcessIncludes(
        IPage $page,
        Template $template
    ): void;
}
