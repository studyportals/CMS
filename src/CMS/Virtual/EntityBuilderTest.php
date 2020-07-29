<?php declare(strict_types=1);

namespace StudyPortals\CMS\Virtual;

use PHPUnit\Framework\TestCase;
use RuntimeException;

class EntityBuilderTest extends TestCase
{

    /**
     * @throws RuntimeException
     */
    public function testConstructVirtualEntityNoId(): void
    {
        $empty = (object) [];
        $emptyObject = EntityBuilder::constructVirtualEntity($empty);
        $this->assertEquals(0, $emptyObject->getId());
    }

    /**
     * @throws RuntimeException
     */
    public function testConstructVirtualEntityWithId(): void
    {
        $empty = (object) ['id' => '1'];
        $emptyObject = EntityBuilder::constructVirtualEntity($empty);
        $this->assertEquals('1', $emptyObject->getId());
    }

    /**
     * @throws RuntimeException
     */
    public function testConstructVirtualEntityOnlyName(): void
    {
        $empty = (object) ['name' => 'name'];
        $emptyObject = EntityBuilder::constructVirtualEntity($empty);
        $this->assertEquals('name', $emptyObject->getTitle());
    }

    /**
     * @throws RuntimeException
     */
    public function testConstructVirtualEntityOnlyTitle(): void
    {
        $empty = (object) ['title' => 'title'];
        $emptyObject = EntityBuilder::constructVirtualEntity($empty);
        $this->assertEquals('title', $emptyObject->getTitle());
    }

    /**
     * @throws RuntimeException
     */
    public function testConstructVirtualEntityWithNameAndTitle(): void
    {
        $empty = (object) [
            'title' => 'title',
            'name' => 'name'
        ];
        $emptyObject = EntityBuilder::constructVirtualEntity($empty);
        $this->assertEquals('title', $emptyObject->getTitle());
    }
}
