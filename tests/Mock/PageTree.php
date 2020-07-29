<?php declare(strict_types=1);

namespace StudyPortals\Tests\Mock;

class PageTree extends \StudyPortals\CMS\PageTree\PageTree
{

    public function getId(): int
    {
        return 0;
    }

    public function getTemplatePath(): string
    {
        return '';
    }

    public function isDisabled(): bool
    {
        return false;
    }

    public function getSubtitle(): string
    {
        return 'Test page';
    }

    public function isFinal(): bool
    {
        return false;
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
            'root'     => [
                'base' => RootPane::class,
            ],
            'includes' => [
                'named' => [
                    'base' => NamedPane::class,
                ],
            ],
        ];
    }

    public function getPageTreeNodeClasses(): array
    {
        return [
            'over-view'   => OverviewPage::class,
            'combination' => CombinationPage::class,
            'final'       => FinalPage::class,
        ];
    }

    public function getErrorPageClass(): string
    {
        return ErrorPage::class;
    }
}
