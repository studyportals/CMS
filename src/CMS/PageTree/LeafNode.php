<?php declare(strict_types=1);

namespace StudyPortals\CMS\PageTree;

abstract class LeafNode extends PageTreeNode
{

    final public function isDisabled(): bool
    {
        return false;
    }

    public function isFinal(): bool
    {
        return false;
    }

    public function getAllNodes(): array
    {
        return [$this];
    }

    /**
     * @param array<string> $path_array
     *
     * @return IPageTreeNode
     */
    public function findNodeByPathArray(array &$path_array): IPageTreeNode
    {
        return $this;
    }

    /**
     * @return array<string>
     */
    final public function getPageTreeNodeClasses(): array
    {
        return [];
    }
}
