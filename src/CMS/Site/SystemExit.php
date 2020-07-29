<?php declare(strict_types=1);

namespace StudyPortals\CMS\Site;

use Exception;

/**
 * Request the system to exit - immediately halting execution.
 *
 * <p>When running in FastCGI-mode, exit() or die() should never be called as
 * they attempt to end the current PHP process. Doing this defeats most of the
 * purpose of running FastCGI-mode and it has the potential to wreak havoc with
 * the web server hosting the FastCGI executable.</br>
 * Instead, when an "exit" is required this exception should be thrown. It will
 * lead to the CMS immediately aborting the current request without exiting the
 * PHP process hosting it.</p>
 *
 * <p><strong>Note:</strong> This approach is only required in web based code,
 * not in console setting. There you can simply call exit(). Throwing a
 * SystemExit will work though...</p>
 */
class SystemExit extends Exception
{

    /**
     * @var string $_requested_by
     */
    protected $_requested_by;

    /**
     * Create a new SystemExit.
     *
     * <p>All parameters are optional and can be omitted. Currently only the
     * {@link $code} argument is used (and only in CLI-mode) to set the exit()
     * status-code.</p>
     *
     * @param string    $message
     * @param integer   $code
     * @param Exception $Previous
     */

    public function __construct(
        $message = '',
        $code = 0,
        Exception $Previous = null
    ) {

        // Attempt to figure out "who" requested the system exit

        $trace = $this->getTrace();
        $origin = array_shift($trace);

        $this->_requested_by = '';

        if (!empty($origin['class'])) {
            $this->_requested_by = "{$origin['class']}::";
        }

        $this->_requested_by .= "{$origin['function']}()";

        if ($this->_requested_by === '()') {
            $this->_requested_by = 'Unknown';
        }

        parent::__construct($message, $code, $Previous);
    }
}
