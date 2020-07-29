<?php declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: robvd
 * Date: 10/01/2019
 * Time: 13:40
 */

namespace StudyPortals\Utils;

interface IConfig
{

    /**
     * @param string $config
     *
     * @return mixed
     * @throws ConfigNonExistentException
     */
    public function retrieve(string $config);
}
