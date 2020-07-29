<?php declare(strict_types=1);

namespace StudyPortals\CMS\Module;

use StudyPortals\CMS\ExceptionHandler;
use StudyPortals\CMS\ModuleCollection\IModuleCollection;
use StudyPortals\CMS\Page\IPage;
use StudyPortals\CMS\Page\Page;

trait ModuleDeprecation
{
    /**
     * @var string
     * @deprecated use ->getModulePath();
     *             deprecation obviously isn't needed for private usage,
     *             this is only to help make the magic get usages stand out as
     *             deprecated
     */
    public $module_path;

    /**
     * @var Page<IModuleCollection>
     * @deprecated use ->getPage();
     *             deprecation obviously isn't needed for private usage,
     *             this is only to help make the magic get usages stand out as
     *             deprecated
     */
    private $Page;

    /**
     * Get a dynamic property.
     *
     * @param string $name
     *
     * @return string|IPage<IModuleCollection>|null
     */

    final public function __get($name)
    {
        if ($name === 'Page') {
            return $this->getPage();
        }

        ExceptionHandler::notice("Undefined variable: $name");
        return null;
    }

    /**
     * Helper function to addHeaderInclude for the current Page.
     *
     * @param string $file
     * @param string $type
     *
     * @return void
     * @deprecated
     */

    public function addHeaderInclude($file, $type = 'javascript'): void
    {
        $this->getPage()->addHeaderInclude($file, $type);
    }

    /**
     * Helper function to addFooterInclude for the current Page
     *
     * @param string $file
     *
     * @return void
     * @deprecated
     */

    public function addFooterInclude($file): void
    {
        $this->addJs($file);
    }
}
