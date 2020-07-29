<?php declare(strict_types=1);

namespace StudyPortals\Tests\Mock;

use StudyPortals\CMS\PageTree\LeafNode;

class ErrorPage extends LeafNode
{

    public function getId(): int
    {
        return 0;
    }

    public function getTemplatePath(): string
    {
        return '';
    }

    public function getSubtitle(): string
    {
        return 'Test Error page';
    }

    public function isFinal(): bool
    {
        return false;
    }

    public function getDescription(): string
    {
        return 'Error';
    }

    protected function getPaneClasses(): array
    {
        return [
            'root' => [
                'base' => RootPane::class,
            ],
            'includes' => [
                'named' => [
                    'base' => NamedPane::class,
                ]
            ]
        ];
    }
}
