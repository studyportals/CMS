<?php declare(strict_types=1);

namespace StudyPortals\CMS\Page;

use StudyPortals\CMS\Definitions\Hooks\IHook;
use StudyPortals\CMS\Definitions\IInputHandler;
use StudyPortals\CMS\ModuleCollection\ModuleCollection;
use StudyPortals\CMS\PageTree\IPageTree;
use StudyPortals\CMS\PageTree\IPageTreeNode;
use StudyPortals\CMS\Site\ISite;
use StudyPortals\Utils\HTTP;

class Page extends ModuleCollection implements IPage
{
    use PagePresentation;
    use PageAssets;
    use PageVirtualization;
    use PageCaching;
    use PageDeprecation;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $subtitle;

    /**
     * @var string
     */
    protected $structuredMarkupData = '';

    /**
     * @var boolean
     */
    protected $pageNotIndexable = false;

    /**
     * @var array<string>
     */
    protected $canonical_parameters = [];

    /**
     * @var string|null
     */
    protected $canonical_url;

    /**
     * @var SubtitleAdditions
     */
    protected $subtitle_additions;

    private $pageTreeNode;

    /**
     * Create a new Page object based on page information from the database.
     *
     * @param IPageTreeNode $pageTreeNode
     *
     * @throws PageNotFoundException
     *
     * @noinspection PhpMissingParentConstructorInspection
     */

    public function __construct(IPageTreeNode $pageTreeNode)
    {

        $this->pageTreeNode = $pageTreeNode;

        $this->subtitle = $pageTreeNode->getSubtitle();
        $this->description = $pageTreeNode->getDescription();

        // Disabled page

        if ($pageTreeNode->isDisabled()) {
            throw new PageNotFoundException(
                "Page with ID {$pageTreeNode->getRoute()} is disabled"
            );
        }

        $this->setModuleCollection(
            $pageTreeNode->constructModuleCollection($this)
        );
        $this->subtitle_additions = new SubtitleAdditions();
        $this->header_includes = new Includes();
        $this->footer_includes = new Includes();
    }

    public function getSubtitle(): string
    {
        return $this->processSubtitleAdditions($this->subtitle);
    }

    /**
     * Modify the subtitle of this page.
     *
     * <p>In its most basic use, this method <em>permanently</em> changes the
     * subtitle of the current page into the provided {@link $subtitle}. If
     * the {@link $append} argument is set to <em>true</em> (defaults to
     * <em>false</em>), the provided subtitle is appended to the subtitle.
     * Only the last appended string will actually be appended to the subtitle
     * upon display of the page.</p>
     *
     * <p>To revert the subtitle to the value as stored in the database, pass in
     * <em>null</em> as argument for {@link $subtitle}. Upon changing or
     * clearing of the page's virtual-page the subtitle is automatically
     * reverted to the value stored in the database.</p>
     *
     * <p>The optional {@link $replaces} array allows for more advanced
     * modifications to the subtitle. Markers in the subtitle (in the form of
     * %marker%) will be replaced by values from the array.<br>
     * The keys in the array should match the markers (excluding the %-signs).
     * When set multiple times, arrays will be merged together, with values
     * from the last array getting preference. Set values to <em>null</em> to
     * clear them from the output.</p>
     *
     * @param string|null   $subtitle
     * @param boolean       $append
     * @param array<string> $replaces
     *
     * @return void
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setSubtitle(
        $subtitle = '',
        $append = false,
        array $replaces = []
    ): void {

        // Revert to the subtitle as stored in database

        if ($subtitle === null) {
            $this->subtitle = $this->getPageTreeNode()->getSubtitle();
            $this->subtitle_additions->reset();

            return;
        }

        // Set a custom subtitle

        $subtitle = trim($subtitle);

        if (!$append && $subtitle !== '') {
            $this->subtitle = $subtitle;
        } elseif ($subtitle !== '') {
            $this->subtitle_additions->setAppend($subtitle);
        }

        if (count($replaces) > 0) {
            $this->subtitle_additions->addReplaces($replaces);
        }
    }

    public function getStructuredMarkupData(): string
    {
        return $this->structuredMarkupData;
    }

    /**
     * Sets the Structured markup data JSON object
     * Schema's could be any of the schema's on http://schema.org/
     *
     * Additional reference
     * https://developers.google.com/search/docs/guides/intro-structured-data
     *
     * @param string $structuredMarkupData
     *
     * @return void
     */
    public function setStructuredMarkupData(string $structuredMarkupData): void
    {
        $this->structuredMarkupData = $structuredMarkupData;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the description of the Page.
     *
     * <p>Under normal circumstances the Page's description is read from the
     * database. This method allows you to overwrite this description. This
     * method operates using the "last-come, first-serve" principle: The last
     * description set is the one displayed to the user.</p>
     *
     * <p>To revert the description to the value as stored in the database,
     * pass
     * in <em>null</em> or an empty string as argument for {@link
     * $description}.
     * Upon changing or clearing of the page's virtual-page the description is
     * automatically reverted to the value stored in the database.</p>
     *
     * <p>The description is primarly used to populate the "meta" description
     * field and as such its length is by default limited to 150 characters,
     * taking into account current SEO best-practices. Modify the {@link
     * $length} parameter to force a custom maximum length.</p>
     *
     * @param string  $description
     * @param integer $length
     */

    public function setDescription(string $description, int $length = 160): void
    {

        $description = trim($description);

        // Revert to the description as stored in database

        if (empty($description)) {
            $this->description = $this->getPageTreeNode()->getDescription();
            return;
        }

        // Set a custom description

        // Limit description length
        $description = str_replace('&nbsp;', ' ', $description);
        $description = wordwrap($description, $length);
        [$description,] = explode("\n", $description, 2);

        // If there's a dot or a comma, attempt to cut at the end of a sentence

        $offset = min(strlen($description), 50);

        $last_dot = strrpos($description, '.', $offset);
        $last_comma = strrpos($description, ',', $offset);

        if ($last_dot > 0 || $last_comma > 0) {
            $end = (($last_dot >= $last_comma) ? $last_dot : $last_comma);

            if ($end !== false) {
                $description = substr($description, 0, $end) . '.';
            }
        }

        $this->description = $description;
    }

    public function setPageNotIndexable(bool $isNotIndexable): void
    {
        $this->pageNotIndexable = $isNotIndexable;
    }

    /**
     * Find the path from the root of the PageTree to this Page.
     *
     * <p>Wrapper function for PageNode::getPath().</p>
     *
     * @return array<IPageTreeNode>
     */

    public function findPath(): array
    {
        return $this->getPageTreeNode()->findPath();
    }

    /**
     * Get the canonical URL for the Page.
     *
     * <p>Returns a URL similar to that produced by Page::getURL() with one
     * major exception: All "canonical" GET parameters are appended to the
     * generated URL in alphabetical order. All other GET parameters are
     * ignored.</p>
     *
     * <p>As a result, this method generates the most basic still unique (c.q.
     * the canonical) URL for the page. The result of this method is mostly
     * useful for SEO purposes and as such is automatically added to the main
     * template through Page::popuplateCommonFields().</p>
     *
     * <p><strong>Note:</strong> When the current Site error-page is loaded the
     * canonical URL will always return an empty string. This is to prevent the
     * error-page from "replacing" the actual content, which could be suffering
     * from a temporary error. When implementing canonical URL features, make
     * sure you take into account the fact that it can potentially be an empty
     * string...</p>
     *
     * @return string
     * @see \StudyPortals\CMS\Page\IPage::getFullURL()
     * @see \StudyPortals\CMS\Page\IPage::addCanonicalParameter()
     */

    public function getCanonicalURL(): ?string
    {

        // The "error-page" should never provide a canonical URL

        $pageTree = $this->getPageTree();
        if ($this->getPageTreeNode() === $pageTree->getErrorPage()) {
            return '';
        }

        if (!is_string($this->canonical_url)) {
            return $this->getFullURL();
        }

        // All variants of the "homepage" should point to our base URL

        $url = $this->canonical_url;

        $query = [];

        $this->canonical_parameters =
            array_unique($this->canonical_parameters);
        $get_parameters = array_map(
            'strtolower',
            array_keys($this->getInputHandler()->getAll(INPUT_GET))
        );

        $canonical_parameters = array_intersect(
            $get_parameters,
            $this->canonical_parameters
        );
        sort($canonical_parameters);

        foreach ($canonical_parameters as $parameter) {
            $query[$parameter] = $this->getInputHandler()->get(
                INPUT_GET,
                $parameter
            );
        }

        $query_string = http_build_query($query);

        if ($query_string !== '') {
            $url .= "?{$query_string}";
        }

        return $url;
    }

    /**
     * Set the canonical URL of the current Page.
     *
     * <p>Under normal circumstances the Page's canonical URL is based upon its
     * actual URL (c.q. determined automatically). This method allows you to
     * overwrite the URL with a custom value. This method operates using the
     * "last-come, first-serve" principle: The last canonical URL set is the
     * one used.</p>
     *
     * <p>The canonical URL is only set if a valid URL (including schema) is
     * provided. In all other cases the current custom canonical URL is cleared
     * and the automatically generated canonical URL is used. Furthermore,
     * whenever the current virtual page ID changes, the custom canonical URL
     * is cleared automatically.<br>
     * It is not possible to overwrite the canonical URL of either the Site
     * error-page or the Site homepage. Be careful when calling this method;
     * setting a wrong canonical URL can have a detrimental effect on a page's
     * search-engine performance...</p>
     *
     * @param string $url
     *
     * @return void
     * @see \StudyPortals\CMS\Page\IPage::getCanonicalURL()
     */

    public function setCanonicalURL(string $url): void
    {

        if (empty($url)) {
            $this->canonical_url = null;
            return;
        }

        $normalised_url = HTTP::normaliseURL($url);

        // Invalid URL provided

        $this->canonical_url = $normalised_url === '' ? null : $normalised_url;
    }

    /**
     * Add a GET parameter to the list of allowed "canonical" parameters.
     *
     * <p>When retrieving the canonical URL for the current page all, but the
     * parameters added through this method, are dropped from the URL. Use this
     * method to ensure important parameters <em>are</em> present.</p>
     *
     * <p><strong>Note:</strong> Parameter names are treated in a case-
     * <strong>insensitive</strong> manner and are added using lower-case only
     * characters!</p>
     *
     * @param array<string> $parameters
     *
     * @return void
     * @see \StudyPortals\CMS\Page\IPage::getCanonicalURL()
     */

    public function addCanonicalParameters(array $parameters): void
    {

        $parameters = array_map('strtolower', $parameters);
        $this->canonical_parameters = array_merge(
            $parameters,
            $this->canonical_parameters
        );
    }

    public function getPageTreeNode(): IPageTreeNode
    {
        return $this->pageTreeNode;
    }

    final public function getPageTree(): IPageTree
    {
        return $this->getPageTreeNode()->getPageTree();
    }

    /**
     * Process additions to this page's subtitle.
     *
     * @param string $subtitle
     *
     * @return string
     * @see \StudyPortals\CMS\Page\IPage::setSubtitle()
     */

    protected function processSubtitleAdditions(string $subtitle): string
    {

        if ($this->subtitle_additions->getAppend() !== '') {
            $subtitle .= " {$this->subtitle_additions->getAppend()}";
        }

        if (strpos($subtitle, '%') !== false) {
            $markers = array_keys($this->subtitle_additions->getReplaces());
            $values = array_values($this->subtitle_additions->getReplaces());

            array_walk(
                $markers,
                static function (&$marker) {

                    $marker = "%$marker%";
                }
            );

            $subtitle = str_replace($markers, $values, $subtitle);
            $subtitle = preg_replace('/%[a-z0-9_]+%/i', '', $subtitle);
        }

        return (string) $subtitle;
    }

    private function getInputHandler(): IInputHandler
    {
        return $this->getSite()->getInputHandler();
    }

    final public function getSite(): ISite
    {
        return $this->getPageTree()->getSite();
    }

    public function getPage(): IPage
    {
        return $this;
    }

    /**
     * @return array<IHook>
     */
    private function getHooks(): array
    {
        return $this->getSite()->getHooks();
    }
}
