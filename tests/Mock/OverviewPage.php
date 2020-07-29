<?php declare(strict_types=1);

namespace StudyPortals\Tests\Mock;

use StudyPortals\CMS\PageTree\OverviewNode;

class OverviewPage extends OverviewNode
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
        return 'Over View';
    }

    public function getDescription(): string
    {
        return 'Over View';
    }

    /**
     * @inheritDoc
     */
    public function getPageTreeNodeClasses(): array
    {
        return [
            'final' => FinalPage::class,
        ];
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

    public function getVirtualPageTreeNodeClass(): string
    {
        return VirtualPage::class;
    }
}
