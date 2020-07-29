<?php declare(strict_types=1);

namespace StudyPortals\CMS\Page;

use StudyPortals\CMS\ExceptionHandler;

/**
 * @suppressWarnings(PHPMD)
 */
trait PageDeprecation
{
    /**
     * @var string
     * @deprecated use ->getPageTreeNode()->getName();
     *             deprecation obviously isn't needed for private usage,
     *             this is only to help make the magic get usages stand out as
     *             deprecated
     */
    protected $name;

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
     * @see        \StudyPortals\CMS\Page\IPage::getPageTreeNode()
     * @see        PageTreeNode::getVirtualPath()
     *
     * @deprecated This function returns only the virtual path, use
     * {@link PageTreeNode::getVirtualPath()} for that, use getFullURL instead
     */
    public function getURL(bool $add_base_url = false): string
    {

        $virtual_path = $this->getPageTreeNode()->getVirtualPath();

        // Virtual Page

        if ($this->virtualEntity !== null) {
            array_push(
                $virtual_path,
                $this->virtualEntity->getId(),
                "{$this->virtualEntity->getTitle()}.html"
            );
        }

        $url = implode('/', $virtual_path);

        if ($this->virtualEntity === null) {
            $url .= '/';
        }

        if ($add_base_url) {
            return $this->getSite()->getBaseUrl() . $url;
        }

        return $url;
    }

    /**
     * Get a dynamic property.
     *
     * @param string $name
     *
     * @return string|null
     */

    public function __get(string $name)
    {

        if ($name === 'name') {
            return $this->getPageTreeNode()->getRoute();
        }
        ExceptionHandler::notice("Invalid property name $name");

        return null;
    }
}
