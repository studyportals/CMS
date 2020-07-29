<?php declare(strict_types=1);

namespace StudyPortals\CMS\Page;

class Includes
{

    public const JAVASCRIPT = 'javascript';

    public const CSS = 'css';

    /**
     * @var array<string, bool>
     */
    private $javascript = [];

    /**
     * @var array<string, bool>
     */
    private $css = [];

    public function alreadyAdded(string $type, string $file): bool
    {
        $target = ($type === self::CSS) ? $this->css : $this->javascript;
        return isset($target[$file]);
    }

    public function removeJavascriptInclude(string $file): void
    {
        unset($this->javascript[$file]);
    }

    public function addInclude(string $type, string $file): void
    {
        if ($type === self::JAVASCRIPT) {
            $this->javascript[$file] = true;
            return;
        }
        $this->css[$file] = true;
    }

    /**
     * @param string $type
     *
     * @return array<string>
     */
    public function getIncludes(string $type): array
    {
        if ($type === self::JAVASCRIPT) {
            return array_keys($this->javascript);
        }
        return array_keys($this->css);
    }
}
