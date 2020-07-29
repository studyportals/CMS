<?php declare(strict_types=1);

namespace StudyPortals\CMS\PageTree;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<IPageTreeNode>
 */
abstract class BranchNode extends PageTreeNode implements IteratorAggregate
{

    /**
     * @var IPageTreeNode[] NodeTree from page.json files
     */
    protected $nodes = [];

    public function __construct(PageTreeNode $Parent, string $route)
    {
        parent::__construct($Parent, $route);

        $this->nodes = [];

        foreach ($this->getPageTreeNodeClasses() as $name => $pageTreeNodeClass) {
            $this->nodes[$name] = new $pageTreeNodeClass($this, $name);
        }
    }

    /**
     * @return string[]
     */
    abstract public function getPageTreeNodeClasses(): array;

    /**
     * @param string $class
     *
     * @return IPageTreeNode
     * @throws NodeNotFoundException
     */
    public function findNodeByClass(string $class): IPageTreeNode
    {
        foreach ($this->nodes as $node) {
            try {
                return $node->findNodeByClass($class);
            } catch (NodeNotFoundException $e) {
                continue;
            }
        }
        return parent::findNodeByClass($class);
    }

    public function isFinal(): bool
    {
        return false;
    }

    public function isDisabled(): bool
    {
        return false;
    }

    /**
     * @return array<IPageTreeNode>
     */
    public function getAllNodes(): array
    {
        $nodeset = array_map(function (IPageTreeNode $node) {
            return $node->getAllNodes();
        }, array_values($this->nodes));

        return array_merge([$this], ...$nodeset);
    }

    /**
     * Protected helper for PageTree::findNodeByPath().
     *
     * @param array<string> $path_array
     *
     * @return IPageTreeNode
     * @throws NodeNotFoundException
     */
    public function findNodeByPathArray(array &$path_array): IPageTreeNode
    {

        if ($this->isFinal() || count($path_array) === 0) {
            return $this;
        }

        $path_node = array_shift($path_array);

        if (empty($this->nodes[$path_node])) {
            throw new NodeNotFoundException(
                'Unable to find path to Node "' . $path_node . '"'
            );
        }

        return $this->nodes[$path_node]->findNodeByPathArray($path_array);
    }

    /**
     * @return Traversable<IPageTreeNode>
     */
    public function getIterator()
    {
        return new ArrayIterator($this->nodes);
    }
}
