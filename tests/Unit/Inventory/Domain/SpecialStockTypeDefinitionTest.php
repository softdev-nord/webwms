<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Inventory\Domain\SpecialStockTypeDefinition;

final class SpecialStockTypeDefinitionTest extends TestCase
{
    public function testItNormalizesAValidDefinition(): void
    {
        $definition = new SpecialStockTypeDefinition(' customer ', 'Kundenbestand', 'OWNER', false);

        self::assertSame('CUSTOMER', $definition->code);
        self::assertSame('Kundenbestand', $definition->name);
        self::assertSame('owner', $definition->kind);
        self::assertFalse($definition->allocatable);
    }

    public function testItRejectsAnUnsupportedKind(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SpecialStockTypeDefinition('TEST', 'Testbestand', 'invalid', true);
    }

    public function testItRejectsAnEmptyCode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SpecialStockTypeDefinition(' ', 'Testbestand', 'special', true);
    }
}
