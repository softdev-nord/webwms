<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Security\V3;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use WebWMS\Platform\Application\ExtensionModuleService;
use WebWMS\Security\V3\LoginFormAuthenticator;
use WebWMS\Security\V3\TenantPermissionUser;

class LoginFormAuthenticatorTest extends TestCase
{
    public function testItBuildsTheTenantScopedEmailIdentifier(): void
    {
        $authenticator = new LoginFormAuthenticator(
            self::createStub(UrlGeneratorInterface::class),
            self::createStub(ExtensionModuleService::class)
        );
        $request = Request::create('/v3/login', 'POST', [
            'tenant_id' => ' tenant-id ',
            'email' => ' ADMIN@Example.COM ',
            'password' => 'secret',
            '_csrf_token' => 'token',
        ]);
        $request->attributes->set('_route', LoginFormAuthenticator::LOGIN_ROUTE);

        self::assertTrue($authenticator->supports($request));
        $badge = $authenticator->authenticate($request)->getBadge(UserBadge::class);
        self::assertInstanceOf(UserBadge::class, $badge);
        self::assertSame('tenant-id|admin@example.com', $badge->getUserIdentifier());
    }

    public function testItRedirectsSuccessfulLoginsToTheV3Dashboard(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects($this->once())->method('generate')->with('v3_dashboard')->willReturn('/v3');
        $authenticator = new LoginFormAuthenticator(
            $urlGenerator,
            self::createStub(ExtensionModuleService::class)
        );
        $user = $this->createStub(TenantPermissionUser::class);
        $user->method('tenantId')->willReturn('tenant-id');
        $user->method('getUserIdentifier')->willReturn('admin@example.com');
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $response = $authenticator->onAuthenticationSuccess(
            Request::create('/v3/login', 'POST'),
            $token,
            'v3',
        );

        self::assertSame('/v3', $response->getTargetUrl());
    }
}
