<?php declare(strict_types=1);

namespace StudyPortals\CMS\Module;

/**
 * Interface CacheableModule.
 */
interface CacheableModule
{

    /**
     * Returns true if the module is allowed to be cached.
     *
     * @return boolean
     */

    public function allowCaching();
}
