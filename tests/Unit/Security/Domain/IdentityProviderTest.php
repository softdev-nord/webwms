<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Security\Domain;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebWMS\Security\Domain\IdentityProvider;

class IdentityProviderTest extends TestCase
{
    public function testItAcceptsSecureOidcConfiguration(): void
    {
        $provider = new IdentityProvider('id', 'tenant', 'company', 'Company Login', 'https://id.example', 'client', 'OIDC_SECRET', ['openid', 'email']);

        self::assertSame('company', $provider->code);
    }

    public function testItRejectsInsecureIssuer(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new IdentityProvider('id', 'tenant', 'company', 'Company Login', 'http://id.example', 'client', 'OIDC_SECRET', ['openid']);
    }
}
