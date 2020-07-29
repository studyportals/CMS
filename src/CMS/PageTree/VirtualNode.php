<?php declare(strict_types=1);

namespace StudyPortals\CMS\PageTree;

abstract class VirtualNode extends LeafNode
{
    /**
     * Find the path to the parent PageTreeNode, ignoring the virtual page node
     *
     * @return array<IPageTreeNode>
     */

    public function findPath(): array
    {
        return $this->getParent()->findPath();
    }
}
