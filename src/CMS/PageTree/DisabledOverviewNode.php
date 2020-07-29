<?php declare(strict_types=1);

namespace StudyPortals\CMS\PageTree;

abstract class DisabledOverviewNode extends OverviewNode
{

    final public function getTemplatePath(): string
    {
        return '';
    }

    final public function isDisabled(): bool
    {
        return true;
    }

    final public function getSubtitle(): string
    {
        return '';
    }

    final public function getDescription(): string
    {
        return '';
    }

    /**
     * @return array<mixed>
     */
    final protected function getPaneClasses(): array
    {
        return [];
    }
}
