<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Inventory\Domain\StorageBinDefinition;

final class StorageBinDefinitionTest extends TestCase
{
    public function testItDescribesALevelAndBinWithCapacity(): void
    {
        $bin = new StorageBinDefinition('A-01-02', '01', '02', 'storage', 100);

        self::assertSame('A-01-02', $bin->code());
        self::assertSame('01', $bin->levelCode());
        self::assertSame('02', $bin->binCode());
        self::assertSame(100, $bin->capacityQuantity());
    }

    public function testItRejectsAnUnsupportedLocationType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StorageBinDefinition('A-01-02', '01', '02', 'unknown', 100);
    }

    public function testItRejectsNegativeCapacity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StorageBinDefinition('A-01-02', '01', '02', 'storage', -1);
    }
}
