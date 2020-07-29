<?php declare(strict_types=1);

namespace StudyPortals\CMS\Page;

use Exception;
use RuntimeException;
use StudyPortals\CMS\Module\ModuleException;
use StudyPortals\CMS\Module\VirtualEntityDependentModule;
use StudyPortals\CMS\Module\VirtualEntityModule;
use StudyPortals\CMS\Site\InvalidURLException;
use StudyPortals\CMS\Virtual\BasicPageEntity;
use StudyPortals\CMS\Virtual\IVirtualPageEntity;
use StudyPortals\CMS\Virtual\VirtualDomainEntityDependentModule;
use StudyPortals\CMS\Virtual\VirtualDomainEntityModule;
use StudyPortals\CMS\Virtual\VirtualPath;
use StudyPortals\CMS\ResourceNotFoundException;

trait PageVirtualization
{

    /**
     * @var IVirtualPageEntity|null
     */
    private $virtualEntity;

    /**
     * @return string|null
     */
    public function getVirtualId(): ?string
    {
        return ($this->virtualEntity) ? $this->virtualEntity->getId() : null;
    }

    public function getVirtualName(): string
    {
        return ($this->virtualEntity) ? $this->virtualEntity->getTitle() : '';
    }

    /**
     * Set a virtual ID for the current Page.
     *
     * @param string $virtual_id
     *
     * @throws Exception
     * @uses \StudyPortals\CMS\Page\IPage::_processTokenAccess()
     * @uses Page::processDependentModules()
     */

    public function setVirtualID(string $virtual_id): void
    {

        $virtual_modules =
            $this->findModulesByClass(VirtualEntityModule::class);
        if (count($virtual_modules) <= 0) {
            throw new PageNotFoundException(
                'None of the modules on this
				page provide virtual-page support'
            );
        }

        // There can be multiple modules; we only use the first

        /** @var VirtualEntityModule $Module */
        $Module = array_shift($virtual_modules);

        try {
            // Set the virtual-page ID

            $Module->setVirtualPageID($virtual_id);
        } catch (ModuleException $ModuleException) {
            // Re-throw exception to facilitate debugging

            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                throw $ModuleException;
            }

            throw new PageNotFoundException(
                'Error while setting
				virtual page ID: ' . $ModuleException->getMessage()
            );
        }

        $virtualPageName = VirtualPath::formatVirtualPageName(
            $Module->getVirtualPageName(),
            ''
        );

        $this->virtualEntity = new BasicPageEntity(
            $virtual_id,
            $virtualPageName
        );

        // Process virtual-entity dependent Modules

        if ($Module instanceof VirtualEntityModule) {
            $this->processDependentModules($Module);
        }

        if ($Module instanceof VirtualDomainEntityModule) {
            $this->processDomainEntityDependentModules($Module->getEntity());
        }
    }

    /**
     * Get the Full URL for this Page.
     *
     * <p>This methods returns the <strong>full</strong> URL of the Page. This
     * includes the domain, the virtual path, the locale and the virtual page
     * where applicable. The URL generated provides a valid link to the specific
     * instance of Page within the current Site context.<br>
     * <strong>Note:</strong> Since this method returns a URL relevant to the
     * current Site context, the domain will only be included if it differs
     * from the current Site domain.</p>
     *
     * <p>If you just require the URL of the page (language independent,
     * excluding virtual page information) use {@link PageTreeNode::getURL()}.
     * <br>
     * If you only require the virtual path use
     * {@link PageTreeNode::getVirtualPath()}.</p>
     *
     * @return string
     * @see \StudyPortals\CMS\Page\IPage::getPageTreeNode()
     * @see PageTreeNode::getVirtualPath()
     */

    public function getFullURL(): string
    {

        if ($this->virtualEntity instanceof IVirtualPageEntity) {
            return $this->getPageTreeNode()->getVirtualPageUrl(
                $this->virtualEntity
            );
        }

        return $this->getPageTreeNode()->getFullUrl();
    }

    /**
     * @param string $name
     *
     * @return void
     *
     * @throws ResourceNotFoundException
     */
    public function checkVirtualName(string $name): void
    {
        if (!($this->virtualEntity instanceof IVirtualPageEntity)) {
            throw new RuntimeException(
                'No VirtualPageEntity found to check name on.'
            );
        }

        if ($this->virtualEntity->getTitle() !== $name) {
            throw new InvalidURLException(
                "Virtual name
						\"{$name}\" is invalid'",
                $this->getFullURL()
            );
        }
    }

    /**
     * Process the Dependent Modules of a VirtualEntityModule.
     *
     * <p>Provides the dependent Modules with the main VirtualEntityModule's
     * virtual-entity and optionally set token-based access-levels.</p>
     *
     * @param VirtualEntityModule $Module
     */

    protected function processDependentModules(
        VirtualEntityModule $Module
    ): void {

        $dependent_modules = $this->findModulesByClass(
            VirtualEntityDependentModule::class
        );

        if (count($dependent_modules) > 0) {
            $VirtualEntity = $Module->getVirtualEntity();

            assert('is_object($VirtualEntity)');
            if (is_object($VirtualEntity)) {

                /** @var VirtualEntityDependentModule $DependentModule */
                foreach ($dependent_modules as $DependentModule) {
                    $DependentModule->setVirtualEntity($VirtualEntity);
                }
            }
        }
    }

    /**
     * Process the Dependent Modules of a VirtualEntityModule.
     *
     * <p>Provides the dependent Modules with the main VirtualEntityModule's
     * virtual-entity and optionally set token-based access-levels.</p>
     *
     * @param IVirtualPageEntity $entity
     *
     * @return void
     */

    private function processDomainEntityDependentModules(IVirtualPageEntity $entity): void
    {

        $dependent_modules = $this->findModulesByClass(
            VirtualDomainEntityDependentModule::class
        );

        /** @var VirtualDomainEntityDependentModule $DependentModule */
        foreach ($dependent_modules as $DependentModule) {
            $DependentModule->setEntity($entity);
        }
    }

    protected function setVirtualPageEntity(
        IVirtualPageEntity $virtualEntity
    ): void {
        $this->virtualEntity = $virtualEntity;
    }
}
