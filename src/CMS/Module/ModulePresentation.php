<?php declare(strict_types=1);

namespace StudyPortals\CMS\Module;

use StudyPortals\CMS\Definitions\IAssetProvider;
use StudyPortals\Template\NodeNotFoundException;
use StudyPortals\Template\Repeater;
use StudyPortals\Template\Template;
use StudyPortals\Template\TemplateException;
use StudyPortals\Template\TemplateNodeTree;

trait ModulePresentation
{

    /**
     * @param TemplateNodeTree $templateNodeTree
     *
     * @return void
     * @throws NodeNotFoundException
     * @throws TemplateException
     */
    public function populateTemplate(TemplateNodeTree $templateNodeTree): void
    {
        if ($this instanceof IAssetProvider) {
            $this->getPage()->addAssets($this);
        }

        $target = $this->getTarget($templateNodeTree);

        // Skip non visible controller modules.

        if ($this->isHiddenModule()) {
            return;
        }

        // Main

        /** @var Template $main */
        $main = $this->displayMain();

        if (!($main instanceof TemplateNodeTree)) {
            $main = new ModuleNode(get_class($this), (string) $main);
        }

        if ($target instanceof \StudyPortals\Template\Module) {
            $moduleLayer = Template::create(
                __DIR__ . DIRECTORY_SEPARATOR . 'Module.tp4'
            );
            $templateNodeTree->replaceChild($target, $moduleLayer);
            $templateNodeTree = $moduleLayer;
        }

        if ($target instanceof Repeater) {
            $templateNodeTree = $target;
        }

        $templateNodeTree->setValue('classes', $this->getClasses());
        $templateNodeTree->setValue('definition', $this->getDefinition());
        $contentNode = $templateNodeTree->getChildByName('Content');
        $templateNodeTree->replaceChild($contentNode, $main);

        if ($templateNodeTree instanceof Repeater) {
            $templateNodeTree->repeat();
        }
    }

    private function isHiddenModule(): bool
    {
        return $this instanceof IControllerModule || $this->isHidden();
    }

    /**
     * Output the Module body.
     * @return string|Template
     */

    abstract public function displayMain();

    private function getClasses(): string
    {
        $moduleClass = get_class($this);
        $classList = [];

        while (
            is_string($moduleClass) &&
            is_subclass_of($moduleClass, self::class)
        ) {
            $classList[] = $moduleClass;
            $moduleClass = get_parent_class($moduleClass);
        }

        $classList = implode(' ', array_reverse($classList));
        return str_replace('\\', '_', $classList);
    }
}
