<?php

declare(strict_types=1);

namespace WebWMS\Administration\Application\Access;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;

final readonly class V3AdministrationService
{
    public function __construct(
        private Connection $connection
    ) {
    }

    /** @return list<array<string, mixed>> */
    public function users(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT u.id, u.email, u.display_name, u.status, u.created_at, '
            . "GROUP_CONCAT(r.code ORDER BY r.code SEPARATOR ', ') role_codes "
            . 'FROM wms_user_account u LEFT JOIN wms_user_role ur ON ur.user_id = u.id '
            . 'LEFT JOIN wms_role r ON r.id = ur.role_id '
            . 'WHERE u.tenant_id = :tenantId '
            . 'GROUP BY u.id, u.email, u.display_name, u.status, u.created_at ORDER BY u.display_name, u.email',
            ['tenantId' => $tenantId],
        );
    }

    /** @return list<array<string, mixed>> */
    public function roles(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT r.id, r.code, r.name, r.created_at, COUNT(DISTINCT ur.user_id) user_count, '
            . "GROUP_CONCAT(DISTINCT rp.permission_key ORDER BY rp.permission_key SEPARATOR ',') permissions "
            . 'FROM wms_role r LEFT JOIN wms_role_permission rp ON rp.role_id = r.id '
            . 'LEFT JOIN wms_user_role ur ON ur.role_id = r.id '
            . 'WHERE r.tenant_id = :tenantId GROUP BY r.id, r.code, r.name, r.created_at '
            . 'ORDER BY r.code',
            ['tenantId' => $tenantId],
        );
    }

    /** @return list<array<string, mixed>> */
    public function apiClients(string $tenantId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT c.id, c.name, c.permissions, c.active, c.created_at, c.last_used_at, '
            . 'c.acting_user_id, u.email acting_user_email '
            . 'FROM wms_api_client c LEFT JOIN wms_user_account u ON u.id = c.acting_user_id '
            . 'WHERE c.tenant_id = :tenantId ORDER BY c.name, c.id',
            ['tenantId' => $tenantId],
        );
    }

    /**
     * @param list<string> $permissions
     *
     * @return array{id: string, credential: string}
     */
    public function createApiClient(
        string $tenantId,
        string $actingUserId,
        string $name,
        array $permissions,
        DateTimeImmutable $now,
    ): array {
        $name = trim($name);
        $permissions = array_values(array_unique($permissions));
        if ($name === '' || mb_strlen($name) > 100 || $permissions === []) {
            throw new \InvalidArgumentException('Name und mindestens eine Berechtigung sind erforderlich.');
        }
        foreach ($permissions as $permission) {
            if (!V3PermissionCatalog::contains($permission)) {
                throw new \InvalidArgumentException('Der API-Client enthält eine unbekannte Berechtigung.');
            }
        }
        if ($this->connection->fetchOne(
            'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId AND status = :status',
            ['userId' => $actingUserId, 'tenantId' => $tenantId, 'status' => 'active'],
        ) === false) {
            throw new \InvalidArgumentException('Der ausführende Benutzer ist nicht aktiv oder gehört nicht zum Mandanten.');
        }
        $clientId = Uuid::v7()->toRfc4122();
        $secret = bin2hex(random_bytes(24));
        $this->connection->insert('wms_api_client', [
            'id' => $clientId,
            'tenant_id' => $tenantId,
            'acting_user_id' => $actingUserId,
            'name' => $name,
            'secret_hash' => hash('sha256', $secret),
            'permissions' => json_encode($permissions, JSON_THROW_ON_ERROR),
            'active' => 1,
            'created_at' => $now->format('Y-m-d H:i:s.u'),
        ]);

        return ['id' => $clientId, 'credential' => $clientId . '.' . $secret];
    }

    public function setApiClientActive(string $tenantId, string $clientId, bool $active): void
    {
        $updated = $this->connection->update(
            'wms_api_client',
            ['active' => $active ? 1 : 0],
            ['id' => $clientId, 'tenant_id' => $tenantId],
        );
        if ($updated !== 1) {
            throw new \InvalidArgumentException('Der API-Client wurde nicht gefunden.');
        }
    }

    public function setUserActive(string $tenantId, string $userId, bool $active, DateTimeImmutable $now): void
    {
        $updated = $this->connection->update(
            'wms_user_account',
            ['status' => $active ? 'active' : 'inactive', 'updated_at' => $now->format('Y-m-d H:i:s.u')],
            ['id' => $userId, 'tenant_id' => $tenantId],
        );
        if ($updated !== 1) {
            throw new \InvalidArgumentException('Der Benutzer wurde nicht gefunden.');
        }
    }

    /** @param list<string> $roleIds */
    public function setUserRoles(string $tenantId, string $actorId, string $userId, array $roleIds, DateTimeImmutable $now): void
    {
        $roleIds = array_values(array_unique($roleIds));
        if ($roleIds === []) {
            throw new \InvalidArgumentException('Mindestens eine Rolle ist erforderlich.');
        }
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $userId, $roleIds, $now): void {
            if ($connection->fetchOne('SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId', ['userId' => $userId, 'tenantId' => $tenantId]) === false) {
                throw new \InvalidArgumentException('Der Benutzer wurde nicht gefunden.');
            }
            $count = (int) $connection->fetchOne('SELECT COUNT(*) FROM wms_role WHERE tenant_id = :tenantId AND id IN (:roleIds)', ['tenantId' => $tenantId, 'roleIds' => $roleIds], ['roleIds' => \Doctrine\DBAL\ArrayParameterType::STRING]);
            if ($count !== count($roleIds)) {
                throw new \InvalidArgumentException('Mindestens eine Rolle gehört nicht zum Mandanten.');
            }
            if ($actorId === $userId && $connection->fetchOne('SELECT 1 FROM wms_role_permission WHERE role_id IN (:roleIds) AND permission_key = :permission', ['roleIds' => $roleIds, 'permission' => 'administration.user.write'], ['roleIds' => \Doctrine\DBAL\ArrayParameterType::STRING]) === false) {
                throw new \InvalidArgumentException('Die eigene Berechtigung zur Benutzerverwaltung darf nicht entfernt werden.');
            }
            $connection->delete('wms_user_role', ['user_id' => $userId]);
            foreach ($roleIds as $roleId) {
                $connection->insert('wms_user_role', ['user_id' => $userId, 'role_id' => $roleId]);
            }
            $connection->update('wms_user_account', ['updated_at' => $now->format('Y-m-d H:i:s.u')], ['id' => $userId, 'tenant_id' => $tenantId]);
            $connection->insert('wms_administration_event', ['id' => Uuid::v7()->toRfc4122(), 'tenant_id' => $tenantId, 'aggregate_type' => 'user', 'aggregate_id' => $userId, 'event_type' => 'roles_changed', 'payload' => json_encode(['role_ids' => $roleIds], JSON_THROW_ON_ERROR), 'performed_by' => $actorId, 'occurred_at' => $now->format('Y-m-d H:i:s.u')]);
        });
    }

    /** @param list<string> $permissions */
    public function updateRole(string $tenantId, string $actorId, string $roleId, string $name, array $permissions, DateTimeImmutable $now): void
    {
        $name = trim($name);
        $permissions = array_values(array_unique($permissions));
        if ($name === '' || mb_strlen($name) > 150 || $permissions === []) {
            throw new \InvalidArgumentException('Name und mindestens eine Berechtigung sind erforderlich.');
        }
        foreach ($permissions as $permission) {
            if (!V3PermissionCatalog::contains($permission)) {
                throw new \InvalidArgumentException('Die Rolle enthält eine unbekannte Berechtigung.');
            }
        }
        $this->connection->transactional(function (Connection $connection) use ($tenantId, $actorId, $roleId, $name, $permissions, $now): void {
            if ($connection->fetchOne('SELECT 1 FROM wms_role WHERE id = :roleId AND tenant_id = :tenantId', ['roleId' => $roleId, 'tenantId' => $tenantId]) === false) {
                throw new \InvalidArgumentException('Die Rolle wurde nicht gefunden.');
            }
            if (!in_array('administration.role.write', $permissions, true) && $connection->fetchOne('SELECT 1 FROM wms_user_role WHERE user_id = :actorId AND role_id = :roleId', ['actorId' => $actorId, 'roleId' => $roleId]) !== false) {
                throw new \InvalidArgumentException('Die eigene Berechtigung zur Rollenverwaltung darf nicht entfernt werden.');
            }
            $connection->update('wms_role', ['name' => $name, 'updated_at' => $now->format('Y-m-d H:i:s.u')], ['id' => $roleId, 'tenant_id' => $tenantId]);
            $connection->delete('wms_role_permission', ['role_id' => $roleId]);
            foreach ($permissions as $permission) {
                $connection->insert('wms_role_permission', ['role_id' => $roleId, 'permission_key' => $permission]);
            }
            $connection->insert('wms_administration_event', ['id' => Uuid::v7()->toRfc4122(), 'tenant_id' => $tenantId, 'aggregate_type' => 'role', 'aggregate_id' => $roleId, 'event_type' => 'permissions_changed', 'payload' => json_encode(['permissions' => $permissions], JSON_THROW_ON_ERROR), 'performed_by' => $actorId, 'occurred_at' => $now->format('Y-m-d H:i:s.u')]);
        });
    }
}
