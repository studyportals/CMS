<?php declare(strict_types=1);

namespace StudyPortals\CMS\Handlers;

use StudyPortals\CMS\Definitions\IOutputHandler;
use StudyPortals\Utils\HTTP;

class OutputHandler implements IOutputHandler
{
    /**
     * @param string   $string
     * @param int|null $http_response_code
     */
    public function addHeader(
        string $string,
        int $http_response_code = null
    ): void {
        if ($http_response_code === null) {
            header($string, false);
            return;
        }
        header($string, false, $http_response_code);
    }

    /**
     * @param string   $string
     * @param int|null $http_response_code
     */
    public function replaceHeader(
        string $string,
        int $http_response_code = null
    ): void {
        if ($http_response_code === null) {
            header($string, true);
            return;
        }
        header($string, true, $http_response_code);
    }

    /**
     * Set HTTP status-code.
     *
     * <p>Sets a HTTP/1.1 compliant header for the requested {@link $status}
     * and returns a short message (e.g. "Not Found") that goes along with the
     * status-code.</p>
     *
     * @param integer $status
     *
     * @return string
     * @see _messages::$_codes
     * @see HTTP::message()
     */

    public function status(int $status): string
    {
        $message = HTTP::getStatusMessage($status);
        $this->replaceHeader("HTTP/1.1 $status $message");
        return $message;
    }
}
