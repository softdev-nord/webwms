<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InboundReceipt;
use WebWMS\Inventory\Domain\InventoryId;

final class InboundReceiptTest extends TestCase
{
    public function testItUsesTheAdvisedQuantityWhenNoActualQuantityWasCaptured(): void
    {
        self::assertSame(5, $this->receipt()->actualQuantity(5));
    }

    public function testItCapturesTheActualQuantityAndNormalizesTheReason(): void
    {
        $receipt = $this->receipt(3, '  damaged package  ');

        self::assertSame(3, $receipt->actualQuantity(5));
        self::assertSame('damaged package', $receipt->discrepancyReason());
    }

    public function testItRejectsANonPositiveActualQuantity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->receipt(0);
    }

    private function receipt(?int $actualQuantity = null, ?string $reason = null): InboundReceipt
    {
        return new InboundReceipt(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf480'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf481'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf482'),
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
            $actualQuantity,
            $reason,
        );
    }
}
