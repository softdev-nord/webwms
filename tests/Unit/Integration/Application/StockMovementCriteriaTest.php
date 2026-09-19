<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\StockMovementCriteria;

final class StockMovementCriteriaTest extends TestCase
{
    public function testItAcceptsACompleteMovementFilter(): void
    {
        $criteria = new StockMovementCriteria('product', 'location', 'transfer', 'transfer_in');

        self::assertSame('product', $criteria->productId);
        self::assertSame('transfer_in', $criteria->movementType);
    }

    #[DataProvider('invalidFilters')]
    public function testItRejectsInvalidFilters(?string $productId, ?string $movementType): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new StockMovementCriteria($productId, null, null, $movementType);
    }

    /** @return iterable<string, array{?string, ?string}> */
    public static function invalidFilters(): iterable
    {
        yield 'blank identifier' => [' ', null];
        yield 'unknown movement type' => [null, 'relocation'];
    }
}
