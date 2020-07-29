<?php declare(strict_types=1);

namespace StudyPortals\CMS\Definitions;

interface IOutputHandler
{
    /**
     * @param string   $string
     * @param int|null $http_response_code
     */
    public function addHeader(
        string $string,
        int $http_response_code = null
    ): void;

    /**
     * @param string   $string
     * @param int|null $http_response_code
     */
    public function replaceHeader(
        string $string,
        int $http_response_code = null
    ): void;

    public function status(int $status): string;
}
