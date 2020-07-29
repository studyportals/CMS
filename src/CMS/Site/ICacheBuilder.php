<?php declare(strict_types=1);

namespace StudyPortals\CMS\Site;

use StudyPortals\Cache\CacheEngine;
use StudyPortals\Utils\IConfig;

interface ICacheBuilder
{
    public function build(IConfig $config): CacheEngine;
}
