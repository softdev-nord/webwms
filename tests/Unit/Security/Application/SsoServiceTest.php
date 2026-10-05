<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Security\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use WebWMS\Security\Application\ExternalIdentity;
use WebWMS\Security\Application\SsoAuthenticationException;
use WebWMS\Security\Application\SsoService;
use WebWMS\Security\Domain\IdentityProvider;

class SsoServiceTest extends TestCase
{
    public function testItLoadsEnabledOidcProviderForTenant(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())->method('fetchAssociative')->willReturn([
            'id' => 'provider', 'tenant_id' => 'tenant', 'code' => 'company', 'name' => 'Company',
            'protocol' => 'oidc', 'issuer_url' => 'https://id.example/', 'client_id' => 'client',
            'client_secret_env' => 'OIDC_SECRET', 'scopes' => 'openid profile email',
        ]);

        $provider = new SsoService($connection)->provider('tenant', 'company');

        self::assertSame('https://id.example', $provider->issuerUrl);
        self::assertSame(['openid', 'profile', 'email'], $provider->scopes);
    }

    public function testItResolvesTheTenantFromAUniqueProviderCode(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())->method('fetchAllAssociative')->willReturn([[
            'id' => 'provider', 'tenant_id' => 'tenant', 'code' => 'company', 'name' => 'Company',
            'protocol' => 'oidc', 'issuer_url' => 'https://id.example/', 'client_id' => 'client',
            'client_secret_env' => 'OIDC_SECRET', 'scopes' => 'openid profile email',
        ]]);

        $provider = new SsoService($connection)->providerByCode('company');

        self::assertSame('tenant', $provider->tenantId);
        self::assertSame('company', $provider->code);
    }

    public function testItRejectsAnAmbiguousProviderCode(): void
    {
        $connection = $this->createStub(Connection::class);
        $connection->method('fetchAllAssociative')->willReturn([
            ['protocol' => 'oidc'],
            ['protocol' => 'oidc'],
        ]);

        $this->expectException(SsoAuthenticationException::class);

        (new SsoService($connection))->providerByCode('company');
    }

    public function testItUsesExistingSubjectMappingIdempotently(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())->method('transactional')->willReturnCallback(
            static fn (callable $callback): string => $callback($connection),
        );
        $connection->expects($this->once())->method('fetchAssociative')->willReturn(['id' => 'user', 'email' => 'user@example.com']);
        $connection->expects($this->never())->method('insert');
        $provider = new IdentityProvider('provider', 'tenant', 'company', 'Company', 'https://id.example', 'client', 'OIDC_SECRET', ['openid']);

        $identifier = new SsoService($connection)->resolveUserIdentifier(
            $provider,
            new ExternalIdentity('subject', 'user@example.com'),
            new DateTimeImmutable(),
        );

        self::assertSame('tenant|user@example.com', $identifier);
    }
}
