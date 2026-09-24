<?php

declare(strict_types=1);

namespace WebWMS\Security\V3;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use WebWMS\Platform\Application\GapClosureService;

final class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    public const LOGIN_ROUTE = 'app_v3_login';

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly GapClosureService $gapClosure,
    ) {
    }

    public function supports(Request $request): bool
    {
        return $request->isMethod('POST') && $request->attributes->get('_route') === self::LOGIN_ROUTE;
    }

    public function authenticate(Request $request): Passport
    {
        $tenantId = trim((string) $request->request->get('tenant_id'));
        $email = strtolower(trim((string) $request->request->get('email')));

        return new Passport(
            new UserBadge($tenantId . '|' . $email),
            new PasswordCredentials((string) $request->request->get('password')),
            [new CsrfTokenBadge('authenticate_v3', (string) $request->request->get('_csrf_token'))],
        );
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName,
    ): Response {
        $user = $token->getUser();
        $tenantId = $user instanceof TenantPermissionUser ? $user->tenantId() : null;
        $this->gapClosure->recordLogin($tenantId, $user->getUserIdentifier(), true, $request->getClientIp(), $request->headers->get('User-Agent'), null, new DateTimeImmutable());

        return new RedirectResponse($this->urlGenerator->generate('v3_dashboard'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $tenantId = trim((string) $request->request->get('tenant_id'));
        $email = strtolower(trim((string) $request->request->get('email')));
        $this->gapClosure->recordLogin($tenantId !== '' ? $tenantId : null, $email, false, $request->getClientIp(), $request->headers->get('User-Agent'), $exception->getMessageKey(), new DateTimeImmutable());

        return parent::onAuthenticationFailure($request, $exception);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
