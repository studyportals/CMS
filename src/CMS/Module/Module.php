<?php declare(strict_types=1);

namespace StudyPortals\CMS\Module;

use ReflectionClass;
use RuntimeException;
use StudyPortals\CMS\Definitions\IAssetProvider;
use StudyPortals\CMS\ModuleCollection\IModuleCollection;
use StudyPortals\CMS\ModuleCollection\ModuleCollection;
use StudyPortals\CMS\Page\IPage;
use StudyPortals\CMS\Page\Page;
use StudyPortals\CMS\PageTree\IPageTree;
use StudyPortals\CMS\Site\Site;

/**
 * Abstract definition of a basic front-end Module.
 *
 * <p>The {@link Module::$Page} property always refers to the Page in which
 * this module is contained. This does not necessarily have to be the same as
 * {@link Site::$Page}, which is the page curently loaded (and set for display)
 * by the Site object.</p>
 *
 * <p>The {@link Module::$Page} refers to the {@link Page} which contains this
 * module instance (<b>not</b> its database instance, but its <b>PHP</b>
 * instance). This property does not have to be set, it can be <i>null</i>.<br>
 * In "out-of-band" situation it might happen that only the module is loaded,
 * without being added to a page. In these cases, the property is simply left
 * empty. Note that
 * {@link Site::$Page} refers to the currently loaded page which, as a result
 * of this, does not have to be equal to {@link Module::$Page}.</p>
 */
abstract class Module extends ModuleCollection
{
    use ModuleDeprecation;
    use ModulePresentation;

    /**
     * @var string $definition
     */
    private $definition = '';
    /**
     * @var bool $hidden
     */
    private $hidden = false;

    private $moduleCollection;

    /**
     * Create a Module instance.
     *
     * @param IModuleCollection $moduleCollection Reference to the containing
     *                                            Pane
     *
     * @param string            $name
     *
     */

    final public function __construct(
        IModuleCollection $moduleCollection,
        string $name
    ) {

        parent::__construct($moduleCollection, $name);

        $this->moduleCollection = $moduleCollection;

        /*
         * Do *not* use error suppression on the below function.
         *
         * Doing so will "eat" all fatal errors that occur while parsing
         * the Module's source file. This will leave you guessing as to why
         * you're getting a blank page...
         */

        // Get current working directory of the web app without trailing slash
        $document_root = getcwd();
        $document_root = str_replace('\\', '/', (string) $document_root);

        // Get class directory
        $reflectionClass = new ReflectionClass(static::class);
        $fileName = (string) $reflectionClass->getFileName();
        $dir_name = dirname($fileName);
        $dir_name = str_replace('\\', '/', $dir_name);

        // Replace document root with "." in
        // class directory and end with a slash
        $module_path = str_replace($document_root, '', $dir_name);
        $module_path = './' . trim($module_path, '/') . '/';

        $this->module_path = $module_path;
    }

    /**
     * Called whenever a module is loaded from the database.
     *
     * @return void
     */

    public function load()
    {
    }

    final public function getPage(): IPage
    {
        return $this->moduleCollection->getPage();
    }

    /**
     * @param string $module_class
     *
     * @return Module[]
     */
    public function findModulesByClass(string $module_class): array
    {
        if ($this instanceof $module_class) {
            return [$this];
        }
        return [];
    }

    /**
     * @return string
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @param string $definition
     *
     * @return Module
     */
    public function setDefinition(string $definition): Module
    {
        $this->definition = $definition;
        return $this;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Hide (or unhide) the Module.
     *
     * <p>Use this method to hide/unhide a module. When called without an
     * argument (or with {@link $hidden} set to <em>true</em>), the module gets
     * hidden. If the optional argument {@link $hidden} is set to
     * <em>false</em>
     * the Module is "unhidden" (c.q. displayed again).</p>
     *
     * <p>A Module which is hidden is still present "in" the page, but will not
     * get any of its "display" methods called and will, as a result, not be
     * able to generate any output. Its input methods are still called
     * though!</p>
     *
     * <p><strong>N.B.</strong>: This is not a security feature! The methods
     * input handling is still invoked, so do <em>not</em> use this method
     * as a security measure. This method is purely intended to prevent a
     * Module's rendering code from getting executed. To fully disable a module
     * (including its input handling use Module::overrideAccessLevels()).</p>
     *
     * @param boolean $hidden
     *
     * @return void
     * @see Module::overrideAccessLevels()
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    final public function hide($hidden = true): void
    {

        $this->hidden = ($hidden ? true : false);
    }

    /**
     * Aliasing function for syntactic sugar purposes.
     *
     * <p>All calls to addHeaderInclude($file, 'css') can be replaced with this.
     * This way, we'll be able to find much faster which JavaScript is still
     * included in the header, and work towards eliminating it.</p>
     *
     * @param string $file
     *
     * @return void
     * @deprecated provide a getAssets function to define assets
     */

    public function addCss($file): void
    {
        if ($this instanceof IAssetProvider) {
            throw new RuntimeException(
                'Cannot implement IAssetProvider ' .
                'and call addCss in the same module'
            );
        }
        $this->getPage()->addHeaderInclude($file, 'css');
    }

    /**
     * Syntactic sugar for addFooterInclude, using a more meaningful name.
     *
     * @param string $file
     *
     * @return void
     * @deprecated provide a getAssets function to define assets
     */

    public function addJs($file): void
    {
        if ($this instanceof IAssetProvider) {
            throw new RuntimeException(
                'Cannot implement IAssetProvider ' .
                'and call addJs in the same module'
            );
        }

        $this->getPage()->addFooterInclude($file);
    }

    final public function getPageTree(): IPageTree
    {
        return $this->getSite()->getPageTree();
    }

    public function getModulePath(): string
    {
        return $this->module_path;
    }
}
