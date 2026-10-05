<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Warehouse\Topology\Domain;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WebWMS\Warehouse\Topology\Domain\StorageLocationCoordinate;

class StorageLocationCoordinateTest extends TestCase
{
    public function testItBuildsTheV2CompatibleCoordinate(): void
    {
        self::assertSame('101000100020017', new StorageLocationCoordinate(101, 1, 2, 17)->value());
    }

    /** @param list<int> $parts */
    #[DataProvider('invalidCoordinates')]
    public function testItRejectsValuesOutsideTheCoordinateFormat(array $parts): void
    {
        $this->expectException(InvalidArgumentException::class);

        new StorageLocationCoordinate(...$parts);
    }

    /** @return iterable<string, array{list<int>}> */
    public static function invalidCoordinates(): iterable
    {
        yield 'warehouse number zero' => [[0, 1, 1, 1]];
        yield 'warehouse number too large' => [[1000, 1, 1, 1]];
        yield 'level zero' => [[101, 0, 1, 1]];
        yield 'slot too large' => [[101, 1, 10000, 1]];
        yield 'depth zero' => [[101, 1, 1, 0]];
    }
}
