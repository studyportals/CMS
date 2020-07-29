<?php declare(strict_types=1);

namespace StudyPortals\Tests\Mock;

use StudyPortals\CMS\PageTree\BranchNode;

class CombinationPage extends BranchNode
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
        return 'combination';
    }

    public function getDescription(): string
    {
        return 'combination';
    }

    /**
     * @return array<string>
     */
    public function getPageTreeNodeClasses(): array
    {
        return [
            'over-view' => OverviewPage::class,
        ];
    }

    protected function getPaneClasses(): array
    {
        return [
            'root' => [
                'base' => RootPane::class,
            ],
            'includes' => [
                'include' => [
                    'base' => NamedPane::class,
                ]
            ]
        ];
    }
}
