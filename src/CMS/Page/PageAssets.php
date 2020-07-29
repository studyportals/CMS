<?php declare(strict_types=1);

namespace StudyPortals\CMS\Page;

use StudyPortals\CMS\ResourceNotFoundException;
use StudyPortals\Template\NodeNotFoundException;
use StudyPortals\Template\Repeater;
use StudyPortals\Template\Section;
use StudyPortals\Template\TemplateException;
use StudyPortals\Utils\HTTP;

trait PageAssets
{

    /**
     * @var Includes
     */
    protected $header_includes;

    /**
     * @var Includes
     */
    protected $footer_includes;

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
     * @throws ResourceNotFoundException
     * @throws PageException
     * @see Module::displayHeader()
     */

    public function addHeaderInclude(
        string $file,
        string $type = Includes::JAVASCRIPT
    ): void {

        if (!self::isValidAsset($file)) {
            return;
        }

        if ($type !== Includes::JAVASCRIPT && $type !== Includes::CSS) {
            throw new PageException(
                "Invalid header include type \"$type\"
					specified"
            );
        }

        if ($this->header_includes->alreadyAdded($type, $file)) {
            return;
        }

        /*
         * If a script is already in the footer includes, move it to
         * the header includes - they have priority.
         */

        if ($this->footer_includes->alreadyAdded(Includes::JAVASCRIPT, $file)) {
            $this->footer_includes->removeJavascriptInclude($file);
            $this->addHeaderInclude($file);
            return;
        }

        $this->header_includes->addInclude($type, $file);
    }

    /**
     * Add an external include (JavaScript) to the Page's footer.
     *
     * <p>Works in identical fashion to
     * {@link \StudyPortals\CMS\Page\IPage::addHeaderInclude()}. The main
     * difference is that CSS is not allowed, and that scripts added to the
     * header always get precedence.</p>
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

    public function addFooterInclude(
        string $file,
        string $type = Includes::JAVASCRIPT
    ): void {

        if (!self::isValidAsset($file)) {
            return;
        }

        if ($type !== Includes::JAVASCRIPT) {
            throw new PageException("Cannot include files of type {$type}");
        }

        if ($this->footer_includes->alreadyAdded(Includes::JAVASCRIPT, $file)) {
            return;
        }

        /*
         * If a file is already included in the header, don't add it to
         * the footer.
         */

        if ($this->header_includes->alreadyAdded(Includes::JAVASCRIPT, $file)) {
            return;
        }

        $this->footer_includes->addInclude(Includes::JAVASCRIPT, $file);
    }

    /**
     * Validate the Asset
     *
     * Internal urls are rewritten to `./dist/$file` when it exists.
     * External (http) urls are not validated or rewritten at all.
     *
     * The Exception is only thrown when $this->getSite()->debug_mode=true
     *
     * @param string $file
     *
     * @return boolean
     * @throws ResourceNotFoundException
     */

    public static function isValidAsset(string &$file): bool
    {

        if (strpos($file, 'http') === 0) {
            // Nothing to rewrite
            return true;
        }

        // Only rewrite if it exists.

        $dist_file = (string) preg_replace('#^\./#', '', $file);
        $dist_file = preg_replace('#^dist/#', '', $dist_file);
        $dist_file = './dist/' . $dist_file;

        if (file_exists($dist_file)) {
            $file = $dist_file;
            return true;
        }

        if (file_exists($file)) {
            return true;
        }

        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            throw new ResourceNotFoundException(
                "File {$file} doesn't exist and is not an URL."
            );
        }

        return false;
    }

    /**
     * Process additional header includes.
     *
     * <p>The optional {@link $skip_js) switch can be used to skip inclusion
     * of JavaScript header-includes (only CSS will be included). This is
     * mostly useful for the CMS Administration where Module JavaScript often
     * interfers with administrative Javascript.</p>
     *
     * @param Section $Header
     *
     * @return void
     * @throws NodeNotFoundException
     * @throws TemplateException
     * @see \StudyPortals\CMS\Page\IPage::addHeaderInclude()
     */

    protected function processHeaderIncludes(Section $Header): void
    {

        assert(
            '$Header->ModuleHeader instanceof \StudyPortals\Template\Repeater'
        );

        $includes = [];

        if ($this->pageNotIndexable) {
            $includes[] = '<meta name="robots" content="noindex">';
        }

        $includes_raw = $this->header_includes->getIncludes(Includes::CSS);
        foreach ($includes_raw as $include) {
            $include = $this->cleanInclude($include);
            $includes[] = "<link rel=\"stylesheet\"
				href=\"{$include}\" type=\"text/css\">";
        }

        $includes_raw =
            $this->header_includes->getIncludes(Includes::JAVASCRIPT);
        foreach ($includes_raw as $include) {
            $include = $this->cleanInclude($include);
            $includes[] = "<script type=\"text/javascript\"
				src=\"{$include}\" packtag=\"header\"></script>";
        }

        /*
         * Promote the usage of addHeaderInclude, but not so that it makes
         * testing impossible.
         */

        $js_includes =
            $this->header_includes->getIncludes(Includes::JAVASCRIPT);
        if (
            !empty($js_includes)
            && defined('DEBUG_MODE')
            && DEBUG_MODE
        ) {
            $count = count($js_includes);
            $script = '<script type="text/javascript">';
            $script .= 'if(console){';
            $script .= "console.warn('%c{$count} scripts still use ";
            $script .= "addHeaderInclude()!', 'color: red; font-size: x-large;";
            $script .= "font-weight: bold; font-family: Open Sans, sans-serif');";
            foreach ($js_includes as $n) {
                $file = $n;
                $script .= "console.debug('$file');";
            }
            $script .= '}';
            $script .= '</script>';

            $includes[] = $script;
        }

        $moduleHeader = $Header->getChildByName('ModuleHeader');
        $moduleHeader->setValue('content_raw', implode('', $includes));

        if ($moduleHeader instanceof Repeater) {
            $moduleHeader->repeat();
        }
    }

    private function cleanInclude(string $include): string
    {
        if (
            $include[0] !== '/'
            && strpos($include, 'http://') !== 0
            && strpos($include, 'https://') !== 0
        ) {
            $include = $this->getSite()->getBaseUrl() . $include;
        }
        return HTTP::normaliseURL($include);
    }

    /**
     * Process additional header includes.
     *
     * <p>The optional {@link $skip_js) switch can be used to skip inclusion
     * of JavaScript footer-includes (only CSS will be included). This is
     * mostly useful for the CMS Administration where Module JavaScript often
     * interferes with administrative Javascript.</p>
     *
     * @param Section $Footer
     *
     * @throws PageException
     * @throws TemplateException
     * @see \StudyPortals\CMS\Page\IPage::addHeaderInclude()
     */

    protected function processFooterIncludes(Section $Footer): void
    {

        if (empty($this->footer_includes)) {
            return;
        }

        if (!($Footer instanceof Section)) {
            throw new PageException('Template has no Footer section.');
        }

        assert(
            '$Footer->ModuleFooter instanceof \StudyPortals\Template\Repeater'
        );

        $moduleFooter = $Footer->getChildByName('ModuleFooter');
        if (!($moduleFooter instanceof Repeater)) {
            return;
        }

        $includes = [];

        $raw_includes = $this->footer_includes->getIncludes(
            Includes::JAVASCRIPT
        );
        foreach ($raw_includes as $include) {
            $include = $this->cleanInclude($include);
            $includes[] =
                "<script type=\"text/javascript\" src=\"{$include}\" packtag=\"footer\"></script>";
        }

        $moduleFooter->setValue('content_raw', implode('', $includes));
        $moduleFooter->repeat();
    }
}
