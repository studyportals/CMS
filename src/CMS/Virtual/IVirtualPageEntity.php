<?php declare(strict_types=1);

namespace StudyPortals\CMS\Virtual;

interface IVirtualPageEntity
{

    public function getId(): string;

    public function getTitle(): string;
}
