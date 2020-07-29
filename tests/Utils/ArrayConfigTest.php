<?php declare(strict_types=1);

namespace StudyPortals\Tests\Utils;

use PHPUnit\Framework\TestCase;
use StudyPortals\Utils\ArrayConfig;

class ArrayConfigTest extends TestCase
{

    public function testRetrieveValue(): void
    {

        $config = new ArrayConfig(['foo' => 'bar']);
        $this->assertEquals('bar', $config->retrieve('foo'));
    }
}
