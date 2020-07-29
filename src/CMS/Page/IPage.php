<?php declare(strict_types=1);

namespace StudyPortals\CMS\Page;

use Exception;
use StudyPortals\CMS\Definitions\IAssetProvider;
use StudyPortals\CMS\Module\Module;
use StudyPortals\CMS\Module\ModuleException;
use StudyPortals\CMS\ModuleCollection\IModuleCollection;
use StudyPortals\CMS\PageTree\IPageTree;
use StudyPortals\CMS\PageTree\IPageTreeNode;
use StudyPortals\CMS\PageTree\PageTreeNode;
use StudyPortals\CMS\Site\InvalidURLException;
use StudyPortals\Template\Template;
use StudyPortals\Template\TemplateNodeTree;

interface IPage extends IModuleCollection
{

    public static function isValidAsset(string &$file): bool;

    /**
     * Set a virtual ID for the current Page.
     *
     * @param string $virtual_id
     *
     * @throws Exception
     * @throws ModuleException
     * @uses DBPage::_processTokenAccess()
     * @uses DBPage::_processDependentModules()
     */
    public function setVirtualID(string $virtual_id): void;

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
     * @param string                $subtitle
     * @param boolean               $append
     * @param array<string, string> $replaces
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setSubtitle(
        $subtitle = '',
        $append = false,
        array $replaces = []
    );

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
    public function setDescription(string $description, int $length = 150): void;

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
     * @see DBPage::getCanonicalURL()
     */
    public function setCanonicalURL(string $url);

    /**
     * Add an external include (e.g. JavaScript, CSS) to the Page's header.
     *
     * <p>All modules have the ability to add custom (c.q. Template'd) content
     * to the Page's header through the {@link Module::displayHeader()} method.
     * In most cases though, the only thing a module needs to add is reference
     * to either an external JavaScript or CSS file. In those cases, it's more
     * efficient to not include an entire template to add one static piece of
     * content. That's where this method comes in.</p>
     *
     * <p>Files included here should be relative to the Site root folder (c.q.
     * the folder where the main Index.php file is located) or start with
     * "http(s)://".</p>
     *
     * <p>Duplicate includes are automatically discarded; there is no harm in
     * calling this method without checking if the file you need included isn't
     * already "on the list". When checking for duplicates the value of
     * {@link $type} is ignored (c.q. the check is purely file-name based).</p>
     *
     * @param string $file
     * @param string $type [javascript|css]
     *
     * @return void
     * @throws PageException
     * @see Module::displayHeader()
     */
    public function addHeaderInclude(string $file, string $type = 'javascript');

    /**
     * Add an external include (JavaScript) to the Page's footer.
     *
     * <p>Works in identical fashion to {@link IPage::addHeaderInclude()}.
     * The main difference is that CSS is not allowed, and that scripts added
     * to the header always get precedence.</p>
     *
     * <p>Scenario: module A uses addHeaderInclude on F.js. Module B uses
     * addFooterInclude on F.js. Regardless of the order on the page of module
     * A and B, the end result is that F.js is only included in the header
     * because that's the safest fallback. To achieve this, both
     * addHeaderInclude and addFooterInclude use deduplication.</p>
     *
     * <p>Files included here should be relative to the Site root folder (c.q.
     * the folder where the main Index.php file is located) or start with
     * "http(s)://".</p>
     *
     * @param string $file
     * @param string $type [javascript|css]
     *
     * @return void
     * @throws PageException
     * @see Module::displayFooter()
     */
    public function addFooterInclude(string $file, string $type = 'javascript');

    /**
     * Find the path from the root of the PageTree to this Page.
     *
     * <p>Wrapper function for PageNode::getPath().</p>
     *
     * @return array<IPageTreeNode>
     */
    public function findPath();

    /**
     * Get the URL for this Page.
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
     * @param boolean $add_base_url
     *
     * @return string
     * @see        DBPage::getPageTreeNode()
     * @see        PageTreeNode::getURL()
     * @see        PageTreeNode::getVirtualPath()
     *
     * @deprecated This function returns only the virtual path, use
     * {@link PageTreeNode::getVirtualPath()} for that, use getFullURL instead
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getURL(bool $add_base_url = false);

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
     * @see DBPage::getPageTreeNode()
     * @see PageTreeNode::getURL()
     * @see PageTreeNode::getVirtualPath()
     */
    public function getFullURL();

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
     * @see DBPage::getFullURL()
     * @see DBPage::addCanonicalParameter()
     */
    public function getCanonicalURL();

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
     * @see DBPage::getCanonicalURL()
     */
    public function addCanonicalParameters(array $parameters);

    /**
     * Get the PageTreeNode of the current Page.
     *
     * @return IPageTreeNode
     */
    public function getPageTreeNode();

    /**
     * Return the fully parsed Template for this Page.
     *
     * <p>This method, together with {@link IPage::_loadTemplate()} does a basic
     * sanity-check on the template's structure using a set of assertions.<br>
     * In a "live" scenario (with assert() disabled), an incorrect template will
     * not yield any errors and will, depending how malformed it is, generate
     * (proper) output.</p>
     * @return Template|string
     */
    public function display();

    /**
     * Populate common Site fields into a template.
     *
     * <p>This method populates a set of commonly used Site replace variables
     * into the provided template:</p>
     * <ul>
     *        <li>title, subtitle, description</li>
     *        <li>base_url, page_url</li>
     *        <li>framework_path, template_path, module_path, data_path</li>
     *        <li>For each package "module_path_PackageName" is added</li>
     *        <li>For each site "base_url_SiteName" is added</li>
     *        <li>virtual_paths_enabled, virtual_path, virtual_page</li>
     *    </ul>
     *
     * <p>This method is automatically applied to the template returned by
     * Page::display(). As a result of variable scoping in Template4, fields
     * filled through this method are also available to all instances of
     * Template4 included into the Page's main template, with one notable
     * exception:<br>
     * T4Repeater "repeats" do <strong>not</strong> have access to the scope
     * since they are, when used inside Module::displayMain(), parsed before
     * the module's template is appended to the main template. In these cases,
     * modules will need to manually call Page::populateCommonFields() on their
     * template.</p>
     *
     * @param TemplateNodeTree $Template
     *
     * @return void
     * @see Module::Display()
     */
    public function populateCommonFields(TemplateNodeTree $Template);

    /**
     * Get time to live for the full page caching.
     *
     * <p>Every module on the page needs to indicate it wants to be cached
     * before the page cache can be enabled.</p>
     *
     * @return integer
     * @throws PageException
     */
    public function getTTL();

    public function getPageTree(): IPageTree;

    public function getVirtualName(): string;

    public function getDescription(): string;

    public function setPageNotIndexable(bool $isNotIndexable): void;

    public function getVirtualId(): ?string;

    public function getSubtitle(): string;

    public function setStructuredMarkupData(string $structuredMarkupData): void;

    public function getStructuredMarkupData(): string;

    public function addAssets(IAssetProvider $assets): void;

    /**
     * @param string $virtualName
     * @throws InvalidURLException
     */
    public function checkVirtualName(string $virtualName): void;
}
