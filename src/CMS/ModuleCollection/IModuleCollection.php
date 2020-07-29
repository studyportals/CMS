<?php declare(strict_types=1);

namespace StudyPortals\CMS\ModuleCollection;

use IteratorAggregate;
use StudyPortals\CMS\Page\IPage;
use StudyPortals\CMS\Site\ISite;
use StudyPortals\Template\NodeNotFoundException;
use StudyPortals\Template\TemplateNodeTree;

/**
 * @extends IteratorAggregate<IModuleCollection>
 */
interface IModuleCollection extends IteratorAggregate
{
    public function getName(): string;

    /**
     * @return IPage<IModuleCollection>
     */
    public function getPage(): IPage;

    public function getSite(): ISite;

    public function populateTemplate(TemplateNodeTree $templateNodeTree): void;

    /**
     * @return void
     */
    public function load();

    /**
     * @param string $module_class
     *
     * @return array<IModuleCollection>
     */
    public function findModulesByClass(string $module_class): array;

    /**
     * @param TemplateNodeTree $template
     *
     * @return TemplateNodeTree
     *
     * @throws NodeNotFoundException
     */
    public function getTarget(TemplateNodeTree $template): TemplateNodeTree;
}
