<?php declare(strict_types=1);

namespace StudyPortals\CMS\Virtual;

class BasicPageEntity implements IVirtualPageEntity
{

    private $id;

    private $title;

    public function __construct(string $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
