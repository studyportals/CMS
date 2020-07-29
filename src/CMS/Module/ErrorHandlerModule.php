<?php declare(strict_types=1);

namespace StudyPortals\CMS\Module;

interface ErrorHandlerModule
{

    /**
     * Provide the error-handler with details on the error that just occured.
     *
     * <p>When applicable the {@link Exception} object that caused the error is
     * passed in.</p>
     *
     * @param integer $status_code
     * @param string  $message
     *
     * @return void
     */

    public function setErrorDetails(
        int $status_code,
        string $message
    ): void;
}
