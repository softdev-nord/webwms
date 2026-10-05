<?php

declare(strict_types=1);

namespace WebWMS\Security\Presentation\Web;

use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Throwable;
use WebWMS\Platform\Application\ExtensionModuleService;
use WebWMS\Security\Application\OidcClient;
use WebWMS\Security\Application\SsoAuthenticationException;
use WebWMS\Security\Application\SsoService;
use WebWMS\Security\V3\DbalUserProvider;
use WebWMS\Security\V3\LoginFormAuthenticator;

#[Route('/v3/sso', name: 'app_v3_sso_')]
class SsoController extends AbstractController
{
    public function __construct(
        private readonly SsoService $sso,
        private readonly OidcClient $oidc,
        private readonly DbalUserProvider $users,
        private readonly Security $security,
        private readonly ExtensionModuleService $extensionModule,
    ) {
    }

    #[Route('/login', name: 'login', methods: ['GET'])]
    public function login(Request $request): RedirectResponse
    {
        $tenantId = '';
        $providerCode = strtolower(trim((string) $request->query->get('provider')));
        if ($providerCode === '') {
            $this->addFlash('error', 'security.sso.missing_provider');

            return $this->redirectToRoute('app_v3_login');
        }

        try {
            $provider = $this->sso->providerByCode($providerCode);
            $tenantId = $provider->tenantId;
            $callbackUrl = $this->generateUrl('app_v3_sso_callback', [
                'tenantId' => $provider->tenantId,
                'providerCode' => $provider->code,
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            $authorization = $this->oidc->authorization($provider, $callbackUrl);
            $request->getSession()->set('webwms_sso_' . $authorization->state, [
                'tenantId' => $provider->tenantId,
                'providerCode' => $provider->code,
                'nonce' => $authorization->nonce,
                'codeVerifier' => $authorization->codeVerifier,
            ]);

            return new RedirectResponse($authorization->url);
        } catch (Throwable $exception) {
            $this->recordFailure($request, $tenantId, $exception);
            $this->addFlash('error', 'security.sso.authentication_failed');

            return $this->redirectToRoute('app_v3_login');
        }
    }

    #[Route('/{tenantId}/{providerCode}/callback', name: 'callback', methods: ['GET'])]
    public function callback(string $tenantId, string $providerCode, Request $request): RedirectResponse
    {
        $state = trim((string) $request->query->get('state'));
        $code = trim((string) $request->query->get('code'));
        $transaction = $request->getSession()->remove('webwms_sso_' . $state);

        try {
            if (!is_array($transaction)
                || ($transaction['tenantId'] ?? null) !== $tenantId
                || ($transaction['providerCode'] ?? null) !== $providerCode
                || !is_string($transaction['nonce'] ?? null)
                || !is_string($transaction['codeVerifier'] ?? null)
                || $code === ''
            ) {
                throw new SsoAuthenticationException('The SSO transaction is invalid or expired.');
            }

            $provider = $this->sso->provider($tenantId, $providerCode);
            $callbackUrl = $this->generateUrl('app_v3_sso_callback', [
                'tenantId' => $tenantId,
                'providerCode' => $providerCode,
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            $identity = $this->oidc->identity(
                $provider,
                $callbackUrl,
                $code,
                $transaction['nonce'],
                $transaction['codeVerifier'],
            );
            $identifier = $this->sso->resolveUserIdentifier($provider, $identity, new DateTimeImmutable());
            $user = $this->users->loadUserByIdentifier($identifier);
            $response = $this->security->login($user, LoginFormAuthenticator::class, 'v3');

            return $response instanceof RedirectResponse ? $response : $this->redirectToRoute('v3_dashboard');
        } catch (Throwable $exception) {
            $this->recordFailure($request, $tenantId, $exception);
            $this->addFlash('error', 'security.sso.authentication_failed');

            return $this->redirectToRoute('app_v3_login');
        }
    }

    private function recordFailure(Request $request, string $tenantId, Throwable $exception): void
    {
        $this->extensionModule->recordLogin(
            $tenantId !== '' ? $tenantId : null,
            null,
            false,
            $request->getClientIp(),
            $request->headers->get('User-Agent'),
            $exception->getMessage(),
            new DateTimeImmutable(),
        );
    }
}
