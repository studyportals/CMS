<?php declare(strict_types=1);

namespace StudyPortals\CMS\Module;

/**
 * Interface PageCacheTTLModule.
 *
 * Only one module on a page may implement this interface.
 */
interface CacheableTTLModule extends CacheableModule
{

    /**
     * Returns ttl for the page cache.
     *
     * @return integer
     */

    public function getTTL();
}
