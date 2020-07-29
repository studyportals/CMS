<?php declare(strict_types=1);

namespace StudyPortals\CMS\PageTree;

use StudyPortals\CMS\ModuleCollection\IModuleCollection;
use StudyPortals\CMS\Page\IPage;
use StudyPortals\CMS\Virtual\IVirtualPageEntity;

interface IPageTreeNode
{
    public function getTemplatePath(): string;

    public function isDisabled(): bool;

    public function getSubtitle(): string;

    /**
     * @param IModuleCollection $page
     *
     * @return IModuleCollection[]
     */
    public function constructModuleCollection(IModuleCollection $page): array;

    public function getPageTree(): IPageTree;

    public function getURL(): string;

    public function getFullUrl(): string;

    public function getVirtualPageUrl(IVirtualPageEntity $entity): string;

    /**
     * @return array<string>
     */
    public function getVirtualPath(): array;

    /**
     * @return array<IPageTreeNode>
     */
    public function findPath(): array;

    public function isFinal(): bool;

    public function getRoute(): string;

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $class
     *
     * @return IPageTreeNode
     * @throws NodeNotFoundException
     */
    public function findNodeByClass(string $class): IPageTreeNode;

    /**
     * @return IPage<IModuleCollection>
     */
    public function buildPage(): IPage;

    public function getParent(): IPageTreeNode;

    /**
     * @return array<IPageTreeNode>
     */
    public function getAllNodes(): array;

    /**
     * @param array<string> $path_array
     *
     * @return IPageTreeNode
     */
    public function findNodeByPathArray(array &$path_array): IPageTreeNode;
}
