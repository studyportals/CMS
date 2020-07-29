<?php declare(strict_types=1);

namespace StudyPortals\CMS\Definitions\Hooks;

use Throwable;

interface IHook
{
    /**
     * @param Throwable $throwable
     */
    public function handleError(Throwable $throwable): void;
}
