<?php declare(strict_types=1);

namespace StudyPortals\Tests\Mock;

use StudyPortals\CMS\PageTree\FinalNode;

class FinalPage extends FinalNode
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
        return 'Test page';
    }

    public function getDescription(): string
    {
        return 'Test';
    }

    /**
     * @return array<string, array<string, array<string, string>|class-string>>
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
