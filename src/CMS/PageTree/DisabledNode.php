<?php declare(strict_types=1);

namespace StudyPortals\CMS\PageTree;

abstract class DisabledNode extends BranchNode
{

    final public function getTemplatePath(): string
    {
        return '';
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

    final public function isDisabled(): bool
    {
        return true;
    }
}
