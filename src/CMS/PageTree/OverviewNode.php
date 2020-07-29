<?php declare(strict_types=1);

namespace StudyPortals\CMS\PageTree;

abstract class OverviewNode extends BranchNode
{

    /**
     * @var VirtualNode $virtualPageNode
     */
    private $virtualPageNode;

    public function __construct(PageTreeNode $parent, string $route)
    {
        parent::__construct($parent, $route);

        $virtualNodeClass = $this->getVirtualPageTreeNodeClass();
        $this->virtualPageNode = new $virtualNodeClass($this, $route);
    }

    abstract public function getVirtualPageTreeNodeClass(): string;

    public function findNodeByClass(string $class): IPageTreeNode
    {
        if ($this->virtualPageNode instanceof $class) {
            return $this->virtualPageNode;
        }
        return parent::findNodeByClass($class);
    }

    public function getPageTreeNodeClasses(): array
    {
        return [];
    }

    public function getAllNodes(): array
    {
        $allNodes = parent::getAllNodes();

        $allNodes[] = $this->getVirtualPageNode();

        return $allNodes;
    }

    public function findNodeByPathArray(array &$path_array): IPageTreeNode
    {
        if (count($path_array) === 0) {
            return $this;
        }

        $path = reset($path_array);

        if (isset($this->nodes[$path])) {
            array_shift($path_array);
            return $this->nodes[$path]->findNodeByPathArray($path_array);
        }

        return $this->getVirtualPageNode();
    }

    public function getVirtualPageNode(): IPageTreeNode
    {
        return $this->virtualPageNode;
    }
}
