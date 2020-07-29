<?php declare(strict_types=1);

namespace StudyPortals\CMS\Page;

use StudyPortals\CMS\ExceptionHandler;
use StudyPortals\CMS\Module\CacheableModule;
use StudyPortals\CMS\Module\CacheableTTLModule;
use StudyPortals\CMS\Module\Module;

trait PageCaching
{

    /**
     * Get time to live for the full page caching.
     *
     * <p>Every module on the page needs to indicate it wants to be cached
     * before the page cache can be enabled.</p>
     *
     * @return integer
     * @throws PageException
     */

    public function getTTL(): int
    {

        $ttl = $this->getSite()->page_cache_ttl;

        // One module per page is allowed to modify the global ttl setting

        $ttl_modules = $this->findModulesByClass(CacheableTTLModule::class);
        $count = count($ttl_modules);

        $cache_intended = false;

        if ($count > 0) {
            // There can be multiple modules; we only use the first

            /** @var CacheableTTLModule $Module */
            $Module = array_shift($ttl_modules);

            if ($count > 1) {
                $class = get_class($Module);
                ExceptionHandler::notice(
                    "Detected $count PageCacheTTLModule implementations,
						using $class if ignored"
                );
            }

            $ttl = $Module->getTTL();

            $cache_intended = true;
        }

        /** @var Module $Module */
        foreach ($this->findModulesByClass(Module::class) as $Module) {
            if ($Module instanceof CacheableModule) {
                if (!$Module->allowCaching()) {
                    $ttl = 0;
                    break;
                }

                continue;
            }

            if ($cache_intended) {
                $class = get_class($Module);
                throw new PageException(
                    "Module $class needs to implement the CacheableModule interface."
                );
            }

            $ttl = 0;
            break;
        }

        assert('$ttl >= 60 || $ttl == 0');

        if ($ttl < 60 && $ttl > 0) {
            $ttl = 60;
        }

        return $ttl;
    }
}
