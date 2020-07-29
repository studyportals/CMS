<?php declare(strict_types=1);

namespace StudyPortals\Tests\Mock;

use StudyPortals\CMS\PageTree\VirtualNode;

class VirtualPage extends VirtualNode
{
    public function getId(): int
    {
        return 0;
    }

    public function getTemplatePath(): string
    {
        return 'Resources/Template.tp4';
    }

    public function getSubtitle(): string
    {
        return 'Virtual page';
    }

    public function getDescription(): string
    {
        return 'Virtual';
    }

    /**
     * @return array<string, array<string, array<string, string>|class-string>>.
     */
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
