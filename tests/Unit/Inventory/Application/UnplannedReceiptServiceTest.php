<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Inventory\Application\UnplannedReceiptService;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\UnplannedReceipt;

final class UnplannedReceiptServiceTest extends TestCase
{
    public function testItAcceptsAndNormalizesAnUnplannedReceipt(): void
    {
        $inventory = $this->createMock(InventoryRepository::class);
        $inventory->expects(self::once())->method('saveUnplannedReceipt')->with(self::callback(
            static fn (UnplannedReceipt $receipt): bool => $receipt->supplierCode() === 'SUP-1' && count($receipt->items()) === 1,
        ));
        $receipt = (new UnplannedReceiptService($inventory))->accept(
            '11111111-1111-4111-8111-111111111111',
            'GR-1',
            'sup-1',
            'Supplier',
            null,
            [[
                'productId' => '22222222-2222-4222-8222-222222222222',
                'locationId' => '33333333-3333-4333-8333-333333333333',
                'quantity' => 5,
            ]],
            '44444444-4444-4444-8444-444444444444',
            new DateTimeImmutable(),
        );

        self::assertSame('GR-1', $receipt->code());
    }
}
