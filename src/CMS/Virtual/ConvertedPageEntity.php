<?php declare(strict_types=1);

namespace StudyPortals\CMS\Virtual;

final class ConvertedPageEntity implements IVirtualPageEntity
{

    /**
     * @var string $id
     */
    private $id;

    /**
     * @var string $title
     */
    private $title;

    /**
     * @var string $name
     */
    private $name;

    public function getId(): string
    {
        return $this->id ?? '0';
    }

    public function getTitle(): string
    {
        return $this->title ?? $this->name;
    }
}
