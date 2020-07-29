<?php declare(strict_types=1);

namespace StudyPortals\CMS\PageTree;

use Exception;
use StudyPortals\CMS\ModuleCollection\IModuleCollection;
use StudyPortals\CMS\Page\IPage;
use StudyPortals\CMS\Page\PageNotFoundException;
use StudyPortals\CMS\Page\PageNotSetException;
use StudyPortals\CMS\Site\InvalidURLException;
use StudyPortals\CMS\Site\ISite;
use StudyPortals\CMS\Virtual\VirtualPath;
use StudyPortals\CMS\Virtual\VirtualPathException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class PageTree extends BranchNode implements IPageTree
{

    /**
     * @var ISite
     */
    private $Site;

    /**
     * @var null|IPage<IModuleCollection> $Page
     */
    private $Page;

    /**
     * @var IPageBuilder
     */
    private $pageBuilder;

    public function __construct()
    {
        $this->pageBuilder = new PageBuilder();
        parent::__construct($this, '');
    }

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
     * @throws PageNotFoundException
     * @throws InvalidURLException
     * @throws VirtualPathException
     * @throws Exception
     * @see IPage::getFullURL()
     */
    public function setPageByPath(string $path): void
    {
        // Attempt to find the requested Page
        try {
            $virtual_path_array = [];
            $PageNode = $this->findNodeByPath($path, $virtual_path_array);

            $this->setPage($PageNode);

            if ($PageNode instanceof FinalNode) {
                return;
            }

            if ($PageNode instanceof VirtualNode) {
                $this->getPage()->setVirtualID(
                    array_shift($virtual_path_array)
                );

                $remainder = $virtual_path_array[0] ?? '.html';
                $virtual_name = substr($remainder, 0, -5);
                $this->getPage()->checkVirtualName(
                    $virtual_name ?: $remainder
                );
                return;
            }

            if ($path === '' || $path === '/') {
                return;
            }

            $endChar = substr($path, -1);
            if ($endChar !== '/') {
                throw new InvalidURLException(
                    'Path without virtual name needs a trailing slash',
                    $this->getPage()->getFullURL()
                );
            }
        } catch (NodeNotFoundException $NodeNotFoundException) {
            throw new PageNotFoundException(
                "PageTreeNode not found at \"$path\""
            );
        }
    }

    public function getErrorPage(): IPageTreeNode
    {
        $errorPageClass = $this->getErrorPageClass();
        return new $errorPageClass($this, 'error');
    }

    /**
     * @return IPage<IModuleCollection>
     * @throws PageNotSetException
     */
    public function getPage(): IPage
    {
        if ($this->Page === null) {
            throw new PageNotSetException(
                'No page set yet, first setPage.'
            );
        }
        return $this->Page;
    }

    /**
     * @return void
     */
    public function setErrorPage(): void
    {
        $this->setPage($this->getErrorPage());
    }

    public function findNodeByPath(
        string $path,
        array &$virtual_path_array = []
    ): IPageTreeNode {
        $virtual_path_array = VirtualPath::explodeVirtualPath($path);
        return $this->findNodeByPathArray($virtual_path_array);
    }

    /**
     * @param IPageTreeNode $pageTreeNode
     *
     * @return void
     */
    private function setPage(IPageTreeNode $pageTreeNode): void
    {
        $this->Page = $pageTreeNode->buildPage();
        $this->Page->load();
    }

    public function getPageTree(): IPageTree
    {
        return $this;
    }

    public function getParent(): IPageTreeNode
    {
        return $this;
    }

    /**
     * Find the page of the current PageTreeNode.
     *
     * @return array<IPageTreeNode>
     */

    public function findPath(): array
    {
        return [];
    }

    public function getSite(): ISite
    {
        return $this->Site;
    }

    /**
     * @param ISite $Site
     */
    public function setSite(ISite $Site): void
    {
        $this->Site = $Site;
    }

    abstract public function getErrorPageClass(): string;

    public function setPageBuilder(IPageBuilder $pageBuilder): void
    {
        $this->pageBuilder = $pageBuilder;
    }

    public function getPageBuilder(): IPageBuilder
    {
        return $this->pageBuilder;
    }
}
