<?php declare(strict_types=1);

namespace StudyPortals\CMS\Site;

use StudyPortals\CMS\ResourceNotFoundException;
use StudyPortals\Utils\HTTP;

/**
 * InvalidURLException.
 *
 * <p>Thrown when the requested URL is invalid, but the correct URL could be
 * determined based upon the invalid request. If the exception goes uncaught,
 * it will cause a <b>permanent</b> redirect to occur to the correct URL.</p>
 */
class InvalidURLException extends ResourceNotFoundException
{

    /**
     * @var string
     */
    private $_correct_url;

    /**
     * @var int $_status_code
     */
    private $_status_code = HTTP::MOVED_PERMANENTLY;

    /**
     * Construct a new Invalid URL Exception.
     *
     * <p>The correct URL should be specified in the {@link $correct_url}
     * parameter. The correct URL can be both an absolute URL, or a path
     * relative to the current site's URL. Any information present in the query
     * string and the fragment will be automatically added to the corrected
     * URL.<br> If the provided URL is not specified a
     * {@link ResourceNotFoundException} will be thrown.</p>
     *
     * @param string $message
     * @param string $correct_url
     * @param null   $status_code
     *
     * @throws ResourceNotFoundException
     */

    public function __construct(
        $message,
        $correct_url = null,
        $status_code = null
    ) {

        if ($correct_url === null) {
            throw new ResourceNotFoundException($this->getMessage());
        }

        if (!empty($status_code)) {
            switch ($status_code) {
                case HTTP::MOVED_PERMANENTLY:
                case HTTP::FOUND:
                case HTTP::TEMPORARY_REDIRECT:
                    $this->_status_code = (int) $status_code;

                    break;

                default:
                    $this->_status_code = HTTP::MOVED_PERMANENTLY;

                    break;
            }
        }

        $filter_var = filter_var($correct_url, FILTER_SANITIZE_URL);
        $this->_correct_url = ($filter_var) ?: '';

        parent::__construct($message);
    }

    /**
     * Return the correct URL for this request.
     *
     * @return string
     */

    public function getCorrectURL(): string
    {

        return $this->_correct_url;
    }

    /**
     * Return the status-code for the redirect.
     *
     * @return integer
     */

    public function getStatusCode(): int
    {
        return $this->_status_code;
    }
}
