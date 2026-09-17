<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Administration\Application\Site\CreateSite;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Application\Site\CreateSite\CreateSiteCommand;
use WebWMS\Administration\Application\Site\CreateSite\CreateSiteHandler;
use WebWMS\Administration\Application\Site\CreateSite\SiteCodeAlreadyExists;
use WebWMS\Administration\Application\Site\CreateSite\TenantNotFound;
use WebWMS\Administration\Domain\Site\Site;
use WebWMS\Administration\Domain\Site\SiteCode;
use WebWMS\Administration\Domain\Site\SiteRepository;
use WebWMS\Administration\Domain\Tenant\Tenant;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Administration\Domain\Tenant\TenantRepository;

final class CreateSiteHandlerTest extends TestCase
{
    private const SITE_ID = '018f6b7f-75d2-7c4e-8c33-31f91b1cf2b9';
    private const TENANT_ID = '018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8';

    public function testItPersistsANewSiteForAnExistingTenant(): void
    {
        $sites = new InMemorySiteRepository();
        $handler = new CreateSiteHandler(new TenantExistenceRepository(true), $sites);

        $site = $handler($this->command());

        self::assertSame($site, $sites->site);
        self::assertSame('BER-01', $site->code()->value());
    }

    public function testItRejectsAMissingTenant(): void
    {
        $handler = new CreateSiteHandler(new TenantExistenceRepository(false), new InMemorySiteRepository());

        $this->expectException(TenantNotFound::class);
        $handler($this->command());
    }

    public function testItRejectsADuplicateCodeWithinTheTenant(): void
    {
        $handler = new CreateSiteHandler(
            new TenantExistenceRepository(true),
            new InMemorySiteRepository(true),
        );

        $this->expectException(SiteCodeAlreadyExists::class);
        $handler($this->command());
    }

    private function command(): CreateSiteCommand
    {
        return new CreateSiteCommand(
            self::SITE_ID,
            self::TENANT_ID,
            'ber-01',
            'Berlin Warehouse',
            'Europe/Berlin',
            new DateTimeImmutable('2026-09-17T10:00:00+00:00'),
        );
    }
}

final class TenantExistenceRepository implements TenantRepository
{
    public function __construct(private readonly bool $exists)
    {
    }

    public function exists(TenantId $id): bool
    {
        return $this->exists;
    }

    public function save(Tenant $tenant): void
    {
    }
}

final class InMemorySiteRepository implements SiteRepository
{
    public ?Site $site = null;

    public function __construct(private readonly bool $exists = false)
    {
    }

    public function existsForTenant(TenantId $tenantId, SiteCode $code): bool
    {
        return $this->exists;
    }

    public function save(Site $site): void
    {
        $this->site = $site;
    }
}
