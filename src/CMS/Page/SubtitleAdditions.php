<?php declare(strict_types=1);

namespace StudyPortals\CMS\Page;

class SubtitleAdditions
{

    /**
     * @var array<string, string>
     */
    private $replaces;

    /**
     * @var string
     */
    private $append;

    public function __construct()
    {
        $this->reset();
    }

    /**
     * @return array<string,string>
     */
    public function getReplaces(): array
    {
        return $this->replaces;
    }

    public function getAppend(): string
    {
        return $this->append;
    }

    /**
     * @param array<string, string> $replaces
     *
     * @return void
     */
    public function addReplaces(array $replaces): void
    {
        $this->replaces = array_merge($this->replaces, $replaces);
    }

    public function setAppend(string $subtitle): void
    {
        $this->append = $subtitle;
    }

    public function reset(): void
    {
        $this->replaces = [];
        $this->append = '';
    }
}
