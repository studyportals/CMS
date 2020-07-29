<?php declare(strict_types=1);

namespace StudyPortals\CMS\PageTree;

use StudyPortals\CMS\ModuleCollection\IModuleCollection;
use StudyPortals\CMS\ModuleCollection\ModuleCollection;
use StudyPortals\CMS\Page\IPage;
use StudyPortals\CMS\Page\Page;
use StudyPortals\CMS\Page\PageNotFoundException;
use StudyPortals\CMS\Site\ISite;
use StudyPortals\CMS\Virtual\EntityBuilder;
use StudyPortals\CMS\Virtual\IVirtualPageEntity;
use StudyPortals\CMS\Virtual\VirtualPath;

abstract class PageTreeNode implements IPageTreeNode
{

    /**
     * @var IPageTreeNode
     */
    protected $parent;

    private $route;

    /**
     * Construct a new PageTreeNode.
     *
     * @param IPageTreeNode $Parent
     * @param string        $route
     */

    public function __construct(IPageTreeNode $Parent, string $route)
    {

        $this->parent = $Parent;
        $this->route = $route;
    }

    final public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * Find the page of the current PageTreeNode.
     *
     * @return array<IPageTreeNode>
     */

    public function findPath(): array
    {

        $parent = $this->getParent();
        if ($parent instanceof self) {
            return array_merge($parent->findPath(), [$this]);
        }

        return [$this];
    }

    /**
     * Get the URL for this Node.
     *
     * @return string
     * @see IPage::getURL()
     */

    public function getURL(): string
    {
        $path = $this->getVirtualPath();
        return implode('/', $path) . '/';
    }

    /**
     * Get the Full URL for this Node including the baseUrl.
     *
     * @return string
     */

    public function getFullURL(): string
    {
        $baseUrl = rtrim($this->getSite()->getBaseUrl(), '/');
        $path = ltrim($this->getURL(), '/');
        return "{$baseUrl}/{$path}";
    }

    /**
     * Get the url for this node and the provided virtual entity.
     *
     * @param IVirtualPageEntity $entity
     *
     * @return string
     *@see EntityBuilder::constructVirtualEntity()
     *
     */
    public function getVirtualPageUrl(IVirtualPageEntity $entity): string
    {
        $page_url = rtrim($this->getFullURL(), '/');
        $virtual_id = $entity->getId();
        $virtual_name = VirtualPath::formatVirtualPageName($entity->getTitle());
        return "{$page_url}/{$virtual_id}/{$virtual_name}";
    }

    /**
     * Return the virtual path from the root of the Site PageTree to this Node.
     *
     * @return array<string>
     */

    public function getVirtualPath(): array
    {

        $path = [];

        foreach ($this->findPath() as $Node) {

            /** @var IPageTreeNode $Node */
            $path[] = $Node->getRoute();
        }

        return $path;
    }

    public function getPageTree(): IPageTree
    {
         return $this->getParent()->getPageTree();
    }

    public function getParent(): IPageTreeNode
    {
        return $this->parent;
    }

    /**
     * @param IModuleCollection $page
     *
     * @return array<IModuleCollection>
     * @throws PageTreeException
     */
    public function constructModuleCollection(IModuleCollection $page): array
    {

        $collection = [];

        $paneClasses = $this->getPaneClasses();

        $this->addToCollection($page, is_array($paneClasses['root']) ? $paneClasses['root'] : [], $collection);
        unset($paneClasses['root']);
        $this->addToCollection($page, is_array($paneClasses['includes']) ? $paneClasses['includes'] : [], $collection);
        unset($paneClasses['includes']);
        $this->addToCollection($page, $paneClasses, $collection);

        return $collection;
    }

    public function findNodeByClass(string $class): IPageTreeNode
    {
        if ($this instanceof $class) {
            return $this;
        }
        throw new NodeNotFoundException("Node with class '$class' not found");
    }

    /**
     * @return IPage<IModuleCollection>
     * @throws PageNotFoundException
     */
    public function buildPage(): IPage
    {
        return $this->getPageTree()->getPageBuilder()->buildPage($this);
    }

    /**
     * @return array<mixed>
     */
    abstract protected function getPaneClasses(): array;

    /**
     * @param IModuleCollection           $page
     * @param array<string|array<string>> $paneClasses
     * @param array<IModuleCollection>    $collection
     *
     * @return void
     * @throws PageTreeException
     */
    private function addToCollection(IModuleCollection $page, array $paneClasses, array &$collection): void
    {
        foreach ($paneClasses as $includeMarker => $classes) {
            if (is_iterable($classes)) {
                $collection[] = new ModuleCollection($page, $includeMarker, $classes);
                continue;
            }

            $ModuleCollection = new $classes($page, $includeMarker);

            if (!($ModuleCollection instanceof IModuleCollection)) {
                throw new PageTreeException(
                    "{$classes} is not an instance of IModuleCollection."
                );
            }

            $collection[] = $ModuleCollection;
        }
    }

    public function getSite(): ISite
    {
        return $this->getPageTree()->getSite();
    }
}
