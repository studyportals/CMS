<?php declare(strict_types=1);

namespace StudyPortals\Utils;

/**
 * @codeCoverageIgnore
 */
class ArrayConfig implements IConfig
{

    /**
     * @var array<string, string>
     */
    private $config;

    /**
     * ArrayConfig constructor.
     *
     * @param array<string, string> $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function retrieve(string $config)
    {

        if (isset($this->config[$config])) {
            return $this->config[$config];
        }

        throw new ConfigNonExistentException(
            "Configuration value for '$config' not found."
        );
    }
}
