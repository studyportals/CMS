<?php declare(strict_types=1);

namespace StudyPortals\CMS\Handlers;

use StudyPortals\CMS\Definitions\IInputHandler;

class InputHandler implements IInputHandler
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private $input = [
        INPUT_GET => [],
        INPUT_COOKIE => [],
        INPUT_SERVER => [],
    ];

    /**
     * @suppressWarnings(PHPMD.Superglobals)
     */
    public function __construct()
    {

        $this->input[INPUT_GET] = $_GET;
        $this->input[INPUT_COOKIE] = $_COOKIE;
        $this->input[INPUT_SERVER] = $_SERVER;
    }


    public function has(int $type, string $key): bool
    {
        return isset($this->input[$type][$key]);
    }

    /**
     * @param int    $type
     * @param string $key
     *
     * @return mixed
     */
    public function get(int $type, string $key)
    {
        return $this->input[$type][$key] ?: null;
    }

    /**
     * @param int $type
     *
     * @return array<string, mixed>
     */
    public function getAll(int $type): array
    {
        return $this->input[$type];
    }

    public function remove(int $type, string $key): void
    {
        unset($this->input[$type][$key]);
    }

    /**
     * @param int    $type
     * @param string $key
     * @param mixed  $value
     */
    public function set(int $type, string $key, $value): void
    {
        $this->input[$type][$key] = $value;
    }
}
