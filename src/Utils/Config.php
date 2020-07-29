<?php declare(strict_types=1);

namespace StudyPortals\Utils;

/**
 * @codeCoverageIgnore
 */
class Config implements IConfig
{

    /**
     * @param string $config
     *
     * @return mixed
     * @throws ConfigNonExistentException
     */

    public function retrieve(string $config)
    {

        if (defined($config)) {
            return constant($config);
        }

        throw new ConfigNonExistentException(
            "Configuration value for '$config' not found."
        );
    }
}
