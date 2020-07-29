<?php declare(strict_types=1);

namespace StudyPortals\CMS\Page;

use StudyPortals\CMS\Site\SiteException;

/**
 * PageNotSetException.
 *
 * <p>Used to indicate an operation was attempted on a Page, whilst
 * no Page was loaded in the Site.</p>
 */
class PageNotSetException extends SiteException
{

}
