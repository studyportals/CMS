<?php declare(strict_types=1);

namespace StudyPortals\CMS\Definitions;

interface IInputHandler
{
    public function has(int $type, string $key): bool;

    /**
     * @param int    $type
     * @param string $key
     *
     * @return mixed
     */
    public function get(int $type, string $key);

    /**
     * @param int $type
     *
     * @return array<mixed>
     */
    public function getAll(int $type): array;

    public function remove(int $type, string $key): void;

    /**
     * @param int    $type
     * @param string $key
     * @param mixed  $value
     */
    public function set(int $type, string $key, $value): void;
}
