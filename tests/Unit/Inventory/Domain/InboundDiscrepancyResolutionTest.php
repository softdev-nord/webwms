<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InboundDiscrepancyResolution;
use WebWMS\Inventory\Domain\InventoryId;

final class InboundDiscrepancyResolutionTest extends TestCase
{
    public function testItAcceptsReleaseAndRejectActions(): void
    {
        self::assertSame('release', $this->resolution('release')->action());
        self::assertSame('reject', $this->resolution('reject')->action());
    }

    public function testItRejectsAnUnsupportedAction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->resolution('ignore');
    }

    private function resolution(string $action): InboundDiscrepancyResolution
    {
        return new InboundDiscrepancyResolution(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf480'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            $action,
            'Reviewed discrepancy',
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf483'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf484'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf485'),
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable(),
        );
    }
}
