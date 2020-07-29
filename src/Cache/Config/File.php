<?php declare(strict_types=1);

namespace StudyPortals\Cache\Config;

use StudyPortals\Cache\CacheConfig;
use StudyPortals\Cache\CacheConfigException;
use StudyPortals\Utils\File as FileUtils;

class File implements CacheConfig
{

    /**
     * @var string
     */
    protected $directory;

    /**
     * File constructor.
     *
     * @param string $store
     *
     * @throws CacheConfigException
     */
    public function __construct(string $store)
    {

        $store = trim($store);
        $this->directory = './' . FileUtils::trimPath($store);

        if (empty($store) || !is_dir($this->directory)) {
            throw new CacheConfigException(
                "Invalid cache-directory '{$this->directory}' provided"
            );
        }
    }

    public function getDirectory(): string
    {

        return $this->directory;
    }
}
