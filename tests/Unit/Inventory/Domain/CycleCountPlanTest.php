<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Access\UserId;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\CycleCountPlan;
use WebWMS\Inventory\Domain\InventoryId;

final class CycleCountPlanTest extends TestCase
{
    public function testItNormalizesItsCodeAndKeepsTheSchedule(): void
    {
        $plan = new CycleCountPlan(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf501'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf401'),
            ' cycle-a ',
            'A-01',
            30,
            new DateTimeImmutable('2026-10-01'),
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable('2026-09-18'),
        );

        self::assertSame('CYCLE-A', $plan->code());
        self::assertSame(30, $plan->intervalDays());
        self::assertSame('2026-10-01', $plan->nextDueAt()->format('Y-m-d'));
    }

    public function testItRejectsAnInvalidInterval(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CycleCountPlan(
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf501'),
            new TenantId('018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8'),
            new InventoryId('018f6b7f-75d2-7c4e-8c33-31f91b1cf401'),
            'CYCLE-A',
            'A-01',
            0,
            new DateTimeImmutable('2026-10-01'),
            new UserId('018f6b7f-75d2-7c4e-8c33-31f91b1cf302'),
            new DateTimeImmutable('2026-09-18'),
        );
    }
}
