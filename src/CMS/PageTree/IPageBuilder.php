<?php declare(strict_types=1);

namespace StudyPortals\CMS\PageTree;

use StudyPortals\CMS\Page\IPage;
use StudyPortals\CMS\Page\PageNotFoundException;

interface IPageBuilder
{

    /**
     * @param IPageTreeNode $node
     *
     * @return IPage
     * @throws PageNotFoundException
     */
    public function buildPage(IPageTreeNode $node): IPage;
}
