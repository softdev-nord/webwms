<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Api;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

final class ApiKeyAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function supports(Request $request): bool
    {
        return str_starts_with($request->getPathInfo(), '/api/v3');
    }

    public function authenticate(Request $request): Passport
    {
        $credential = trim((string) $request->headers->get('X-API-Key'));
        [$clientId, $secret] = array_pad(explode('.', $credential, 2), 2, '');
        if ($clientId === '' || $secret === '') {
            throw new CustomUserMessageAuthenticationException('A valid X-API-Key header is required.');
        }

        return new SelfValidatingPassport(new UserBadge(
            $clientId,
            fn (string $identifier): ApiClientUser => $this->loadClient($identifier, $secret),
        ));
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName,
    ): ?Response {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return $this->unauthorized($exception->getMessageKey());
    }

    public function start(Request $request, ?AuthenticationException $authException = null): JsonResponse
    {
        return $this->unauthorized($authException?->getMessageKey() ?? 'Authentication is required.');
    }

    private function loadClient(string $clientId, string $secret): ApiClientUser
    {
        $client = $this->connection->fetchAssociative(
            'SELECT id, tenant_id, secret_hash, permissions FROM wms_api_client '
            . 'WHERE id = :id AND active = 1',
            ['id' => $clientId],
        );
        if ($client === false || !hash_equals((string) $client['secret_hash'], hash('sha256', $secret))) {
            throw new CustomUserMessageAuthenticationException('The API credential is invalid.');
        }

        try {
            $decoded = json_decode((string) $client['permissions'], true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new CustomUserMessageAuthenticationException('The API client permissions are invalid.');
        }
        if (!is_array($decoded)) {
            throw new CustomUserMessageAuthenticationException('The API client permissions are invalid.');
        }

        $permissions = [];
        foreach ($decoded as $permission) {
            if (!is_string($permission)) {
                throw new CustomUserMessageAuthenticationException('The API client permissions are invalid.');
            }
            $permissions[] = $permission;
        }

        $this->connection->update(
            'wms_api_client',
            ['last_used_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s.u')],
            ['id' => $clientId],
        );

        return new ApiClientUser((string) $client['id'], (string) $client['tenant_id'], $permissions);
    }

    private function unauthorized(string $detail): JsonResponse
    {
        return new JsonResponse([
            'type' => 'about:blank',
            'title' => 'Unauthorized',
            'status' => Response::HTTP_UNAUTHORIZED,
            'detail' => $detail,
        ], Response::HTTP_UNAUTHORIZED, ['Content-Type' => 'application/problem+json']);
    }
}
