<?php declare(strict_types=1);

namespace StudyPortals\CMS\ModuleCollection;

use ArrayIterator;
use StudyPortals\CMS\Page\IPage;
use StudyPortals\CMS\Site\ISite;
use StudyPortals\CMS\Pane;
use StudyPortals\Template\NodeNotFoundException;
use StudyPortals\Template\Repeater;
use StudyPortals\Template\TemplateNodeTree;
use Traversable;

class ModuleCollection implements IModuleCollection
{

    private $name;

    private $parent;

    /**
     * @var IModuleCollection[]
     */
    private $moduleCollections = [];

    /**
     * ModuleCollection constructor.
     *
     * @param IModuleCollection       $parent
     * @param string                  $name
     * @param iterable<string|array>  $moduleCollections
     */
    public function __construct(
        IModuleCollection $parent,
        string $name,
        iterable $moduleCollections = []
    ) {
        $this->parent = $parent;
        $this->name = $name;
        $this->loadCollections($moduleCollections);
    }

    /**
     * @param iterable<string|array> $moduleCollections
     *
     * @return void
     */
    private function loadCollections(iterable $moduleCollections): void
    {
        foreach ($moduleCollections as $name => $moduleCollClass) {
            if (is_iterable($moduleCollClass)) {
                $this->moduleCollections[] = new ModuleCollection($this, $name, $moduleCollClass);
                continue;
            }
            $this->moduleCollections[] = new $moduleCollClass($this, (string) $name);
        }
    }

    /**
     * @return Traversable<IModuleCollection>
     */
    public function getIterator()
    {
        return new ArrayIterator($this->moduleCollections);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPage(): IPage
    {
        return $this->getParent()->getPage();
    }

    public function getSite(): ISite
    {
        return $this->getPage()->getSite();
    }

    /**
     * @param TemplateNodeTree $templateNodeTree
     *
     * @throws NodeNotFoundException
     */
    public function populateTemplate(TemplateNodeTree $templateNodeTree): void
    {
        $target = $this->getTarget($templateNodeTree);
        foreach ($this->moduleCollections as $moduleCollection) {
            $moduleCollection->populateTemplate($target);
        }
    }

    /**
     * @return void
     */
    public function load()
    {
        foreach ($this->moduleCollections as $moduleCollection) {
            $moduleCollection->load();
        }
    }

    /**
     * Find all Modules that are an instance of the specified class.
     *
     * <p>Setting the optional {@link $forced} parameter to <em>true</em> will
     * forces this method to return <strong>all</strong> matching modules, even
     * if they are currently inaccessible due to access-rights restrictions.</p>
     *
     * @param string $module_class
     *
     * @return array<IModuleCollection>
     * @uses Pane::findModulesByClass()
     */

    public function findModulesByClass(string $module_class): array
    {

        $modules = [];

        foreach ($this->moduleCollections as $collection) {
            $modules = array_merge(
                $modules,
                $collection->findModulesByClass($module_class)
            );
        }

        return $modules;
    }

    /**
     * @param TemplateNodeTree $template
     *
     * @return TemplateNodeTree
     * @throws NodeNotFoundException
     */
    public function getTarget(TemplateNodeTree $template): TemplateNodeTree
    {
        if ($template instanceof Repeater) {
            return $template;
        }

        return $template->getChildByName($this->getName());
    }

    /**
     * @param IModuleCollection[] $collections
     *
     * @return void
     */
    protected function setModuleCollection(array $collections): void
    {
        $this->moduleCollections = $collections;
    }

    /**
     * @return IModuleCollection[]
     */
    protected function getModuleCollections(): array
    {
        return $this->moduleCollections;
    }

    private function getParent(): IModuleCollection
    {
        return $this->parent;
    }
}
