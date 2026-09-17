<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Administration\Domain\Site;

use DateTimeImmutable;
use DomainException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Domain\Site\Site;
use WebWMS\Administration\Domain\Site\SiteCode;
use WebWMS\Administration\Domain\Site\SiteCreated;
use WebWMS\Administration\Domain\Site\SiteId;
use WebWMS\Administration\Domain\Site\SiteStatus;
use WebWMS\Administration\Domain\Tenant\TenantId;

final class SiteTest extends TestCase
{
    private const SITE_ID = '018f6b7f-75d2-7c4e-8c33-31f91b1cf2b9';
    private const TENANT_ID = '018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8';

    public function testItCreatesAnActiveSiteAndRecordsTheEvent(): void
    {
        $now = new DateTimeImmutable('2026-09-17T10:00:00+00:00');
        $site = Site::create(
            new SiteId(self::SITE_ID),
            new TenantId(self::TENANT_ID),
            new SiteCode(' ber-01 '),
            ' Berlin Warehouse ',
            'Europe/Berlin',
            $now,
        );

        self::assertSame('BER-01', $site->code()->value());
        self::assertSame('Berlin Warehouse', $site->name());
        self::assertSame('Europe/Berlin', $site->timezone());
        self::assertSame(SiteStatus::Active, $site->status());
        self::assertSame($now, $site->createdAt());

        $events = $site->releaseEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(SiteCreated::class, $events[0]);
        self::assertSame(self::TENANT_ID, $events[0]->tenantId()->value());
    }

    public function testItRejectsAnInvalidTimezone(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Site::create(
            new SiteId(self::SITE_ID),
            new TenantId(self::TENANT_ID),
            new SiteCode('BER-01'),
            'Berlin Warehouse',
            'Berlin',
            new DateTimeImmutable(),
        );
    }

    public function testAnInactiveSiteCannotBeChanged(): void
    {
        $site = Site::create(
            new SiteId(self::SITE_ID),
            new TenantId(self::TENANT_ID),
            new SiteCode('BER-01'),
            'Berlin Warehouse',
            'Europe/Berlin',
            new DateTimeImmutable(),
        );
        $site->deactivate(new DateTimeImmutable('+1 minute'));

        $this->expectException(DomainException::class);
        $site->changeDetails('Hamburg Warehouse', 'Europe/Berlin', new DateTimeImmutable('+2 minutes'));
    }

    public function testItCanBeReactivated(): void
    {
        $site = Site::create(
            new SiteId(self::SITE_ID),
            new TenantId(self::TENANT_ID),
            new SiteCode('BER-01'),
            'Berlin Warehouse',
            'Europe/Berlin',
            new DateTimeImmutable(),
        );
        $site->deactivate(new DateTimeImmutable('+1 minute'));
        $reactivatedAt = new DateTimeImmutable('+2 minutes');

        $site->activate($reactivatedAt);

        self::assertSame(SiteStatus::Active, $site->status());
        self::assertSame($reactivatedAt, $site->updatedAt());
    }
}
