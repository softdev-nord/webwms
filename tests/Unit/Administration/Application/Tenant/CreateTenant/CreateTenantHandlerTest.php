<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Administration\Application\Tenant\CreateTenant;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Administration\Application\Tenant\CreateTenant\CreateTenantCommand;
use WebWMS\Administration\Application\Tenant\CreateTenant\CreateTenantHandler;
use WebWMS\Administration\Application\Tenant\CreateTenant\TenantAlreadyExists;
use WebWMS\Administration\Domain\Tenant\Tenant;
use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Administration\Domain\Tenant\TenantRepository;

final class CreateTenantHandlerTest extends TestCase
{
    private const TENANT_ID = '018f6b7f-75d2-7c4e-8c33-31f91b1cf2b8';

    public function testItPersistsTheNewTenant(): void
    {
        $repository = new InMemoryTenantRepository();
        $handler = new CreateTenantHandler($repository);

        $tenant = $handler(new CreateTenantCommand(
            self::TENANT_ID,
            'SoftDev Nord',
            new DateTimeImmutable('2026-09-17T10:00:00+00:00'),
        ));

        self::assertSame($tenant, $repository->tenant);
    }

    public function testItRejectsADuplicateTenantId(): void
    {
        $repository = new InMemoryTenantRepository(true);
        $handler = new CreateTenantHandler($repository);

        $this->expectException(TenantAlreadyExists::class);
        $handler(new CreateTenantCommand(self::TENANT_ID, 'SoftDev Nord', new DateTimeImmutable()));
    }
}

final class InMemoryTenantRepository implements TenantRepository
{
    public ?Tenant $tenant = null;

    public function __construct(private readonly bool $exists = false)
    {
    }

    public function exists(TenantId $id): bool
    {
        return $this->exists;
    }

    public function save(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }
}
