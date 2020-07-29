<?php declare(strict_types=1);

namespace StudyPortals\CMS;

use StudyPortals\Utils\HTTP;

/**
 * @codeCoverageIgnore
 */
class ResourceNotFoundException extends \Exception
{

    /**
     * Get the statuscode of this exception.
     * @return integer
     */

    public function getStatusCode(): int
    {
        return HTTP::NOT_FOUND;
    }

    /**
     * Get the message that belongs to this status code.
     * @return string
     */

    public function getStatusMessage(): string
    {
        return HTTP::getStatusMessage($this->getStatusCode());
    }
}
