<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Administration\Domain\Tenant;

use DateTimeImmutable;
use DomainException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Tenant\Tenant;
use WebWMS\Administration\Domain\Tenant\TenantCreated;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Administration\Domain\Tenant\TenantStatus;

final class TenantTest extends TestCase
{
    private const TENANT_ID = '018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8';

    public function testItCreatesAnActiveTenantAndRecordsTheEvent(): void
    {
        $now = new DateTimeImmutable('2026-09-17T10:00:00+00:00');
        $tenant = Tenant::create(new TenantId(self::TENANT_ID), ' SoftDev Nord ', $now);

        self::assertSame('SoftDev Nord', $tenant->name());
        self::assertSame(TenantStatus::Active, $tenant->status());
        self::assertSame($now, $tenant->createdAt());
        self::assertSame($now, $tenant->updatedAt());

        $events = $tenant->releaseEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(TenantCreated::class, $events[0]);
        self::assertSame(self::TENANT_ID, $events[0]->tenantId()->value());
    }

    public function testItRejectsAnEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Tenant::create(new TenantId(self::TENANT_ID), '  ', new DateTimeImmutable());
    }

    public function testAnInactiveTenantCannotBeRenamed(): void
    {
        $tenant = Tenant::create(new TenantId(self::TENANT_ID), 'SoftDev Nord', new DateTimeImmutable());
        $tenant->deactivate(new DateTimeImmutable('+1 minute'));

        $this->expectException(DomainException::class);
        $tenant->rename('Another name', new DateTimeImmutable('+2 minutes'));
    }
}
