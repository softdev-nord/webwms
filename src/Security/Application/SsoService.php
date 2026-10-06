<?php

declare(strict_types=1);

namespace WebWMS\Security\Application;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use WebWMS\Security\Domain\IdentityProvider;

readonly class SsoService
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function provider(string $tenantId, string $code): IdentityProvider
    {
        $row = $this->connection->fetchAssociative(
            'SELECT id, tenant_id, code, name, protocol, issuer_url, client_id, client_secret_env, scopes '
            . 'FROM wms_identity_provider WHERE tenant_id = :tenantId AND code = :code AND enabled = 1',
            ['tenantId' => $tenantId, 'code' => strtolower(trim($code))],
        );
        if ($row === false || $row['protocol'] !== 'oidc') {
            throw new SsoAuthenticationException('The active OIDC provider was not found.');
        }

        return $this->createProvider($row);
    }

    public function providerByCode(string $code): IdentityProvider
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT id, tenant_id, code, name, protocol, issuer_url, client_id, client_secret_env, scopes '
            . 'FROM wms_identity_provider WHERE code = :code AND enabled = 1',
            ['code' => strtolower(trim($code))],
        );
        if (count($rows) !== 1 || $rows[0]['protocol'] !== 'oidc') {
            throw new SsoAuthenticationException('The active OIDC provider was not found or is ambiguous.');
        }

        return $this->createProvider($rows[0]);
    }

    public function resolveUserIdentifier(IdentityProvider $provider, ExternalIdentity $identity, DateTimeImmutable $now): string
    {
        return $this->connection->transactional(function (Connection $connection) use ($provider, $identity, $now): string {
            $mapped = $connection->fetchAssociative(
                'SELECT u.id, u.email FROM wms_external_identity e INNER JOIN wms_user_account u ON u.id = e.user_id '
                . 'WHERE e.tenant_id = :tenantId AND e.identity_provider_id = :providerId AND e.subject = :subject AND u.status = :status',
                ['tenantId' => $provider->tenantId, 'providerId' => $provider->id, 'subject' => $identity->subject, 'status' => 'active'],
            );
            if ($mapped !== false) {
                return $provider->tenantId . '|' . (string) $mapped['email'];
            }

            $user = $connection->fetchAssociative(
                'SELECT id, email FROM wms_user_account WHERE tenant_id = :tenantId AND email = :email AND status = :status FOR UPDATE',
                ['tenantId' => $provider->tenantId, 'email' => strtolower($identity->email), 'status' => 'active'],
            );
            if ($user === false) {
                throw new SsoAuthenticationException('The external identity is not assigned to an active tenant user.');
            }

            $identityId = Uuid::v7()->toRfc4122();
            $connection->insert('wms_external_identity', [
                'id' => $identityId,
                'tenant_id' => $provider->tenantId,
                'identity_provider_id' => $provider->id,
                'user_id' => (string) $user['id'],
                'subject' => $identity->subject,
                'created_at' => $now->format('Y-m-d H:i:s.u'),
            ]);
            $connection->insert('wms_administration_event', [
                'id' => Uuid::v7()->toRfc4122(),
                'tenant_id' => $provider->tenantId,
                'aggregate_type' => 'external_identity',
                'aggregate_id' => $identityId,
                'event_type' => 'linked',
                'payload' => json_encode(['provider' => $provider->code, 'subject' => $identity->subject], JSON_THROW_ON_ERROR),
                'performed_by' => (string) $user['id'],
                'occurred_at' => $now->format('Y-m-d H:i:s.u'),
            ]);

            return $provider->tenantId . '|' . (string) $user['email'];
        });
    }

    /** @param array<string, mixed> $row */
    private function createProvider(array $row): IdentityProvider
    {
        $scopes = preg_split('/\s+/', trim((string) $row['scopes']));
        if (!is_array($scopes)) {
            throw new SsoAuthenticationException('The OIDC scopes are invalid.');
        }

        return new IdentityProvider(
            (string) $row['id'],
            (string) $row['tenant_id'],
            (string) $row['code'],
            (string) $row['name'],
            rtrim((string) $row['issuer_url'], '/'),
            (string) $row['client_id'],
            (string) $row['client_secret_env'],
            array_values(array_filter($scopes, static fn (string $scope): bool => $scope !== '')),
        );
    }
}
