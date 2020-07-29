<?php declare(strict_types=1);

namespace StudyPortals\CMS\PageTree;

use StudyPortals\CMS\ModuleCollection\IModuleCollection;
use StudyPortals\CMS\Page\IPage;
use StudyPortals\CMS\Page\PageNotFoundException;
use StudyPortals\CMS\Site\InvalidURLException;
use StudyPortals\CMS\Site\ISite;
use StudyPortals\CMS\Virtual\VirtualPathException;
use StudyPortals\CMS\ResourceNotFoundException;

interface IPageTree extends IPageTreeNode
{

    public function getSite(): ISite;

    public function setSite(ISite $site): void;

    public function getErrorPage(): IPageTreeNode;

    /**
     * @return IPage<IModuleCollection>
     */
    public function getPage(): IPage;

    public function setErrorPage(): void;

    /**
     * Set the active Page based on a virtual path.
     *
     * <p>A virtual path can consist of several levels of virtual path
     * hierarchy, a local string, a virtual ID and a virtual name. The latter
     * three elements are optional.</p>
     *
     * <p>The virtual name of a Page may change over time as it is often based
     * upon dynamic data provided by one of the Page's modules or by the Site
     * in general. In case the requested virtual path does not match up with
     * what was expected, a {@link InvalidURLException} is thrown. This
     * exception contains a reference to the correct URL and, if handled
     * properly, can be considered <strong>non-fatal</strong>.</p>
     *
     * <p>The optional second argument {@link $token} is used to pass access-
     * tokens on to instances of {@link VirtualPageTokenModule} that <em>
     * might</em> be present on the Page being set. Access-tokens supplement
     * the regular Module access system by allowing one-off or time-limited
     * access rights to Modules.</p>
     *
     * @param string $path
     *
     * @return void
     *
     * @throws InvalidURLException
     * @throws PageNotFoundException
     * @throws ResourceNotFoundException
     * @throws VirtualPathException
     * @see IPage::getFullURL()
     */
    public function setPageByPath(string $path): void;

    /**
     * @param string $path
     * @param array<string>  $virtual_path_array
     *
     * @return IPageTreeNode
     */
    public function findNodeByPath(
        string $path,
        array &$virtual_path_array
    ): IPageTreeNode;

    public function setPageBuilder(IPageBuilder $pageBuilder): void;
    public function getPageBuilder(): IPageBuilder;
}
