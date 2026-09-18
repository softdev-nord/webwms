<?php

declare(strict_types=1);

namespace WebWMS\Administration\Infrastructure\Persistence;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use WebWMS\Administration\Domain\Access\Role;
use WebWMS\Administration\Domain\Access\RoleId;
use WebWMS\Administration\Domain\Access\RoleRepository;
use WebWMS\Administration\Domain\Access\UserAccount;
use WebWMS\Administration\Domain\Access\UserAccountRepository;
use WebWMS\Administration\Domain\Tenant\TenantId;

final readonly class DoctrineAccessRepository implements RoleRepository, UserAccountRepository
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function existsByCode(TenantId $tenantId, string $code): bool
    {
        return $this->connection->fetchOne(
            'SELECT 1 FROM wms_role WHERE tenant_id = :tenantId AND code = :code',
            ['tenantId' => $tenantId->value(), 'code' => $code],
        ) !== false;
    }

    public function allExistForTenant(TenantId $tenantId, array $roleIds): bool
    {
        if ($roleIds === []) {
            return true;
        }

        $ids = array_map(static fn (RoleId $roleId): string => $roleId->value(), $roleIds);
        $count = $this->connection->fetchOne(
            'SELECT COUNT(*) FROM wms_role WHERE tenant_id = :tenantId AND id IN (:ids)',
            ['tenantId' => $tenantId->value(), 'ids' => $ids],
            ['ids' => ArrayParameterType::STRING],
        );

        return (int) $count === count($ids);
    }

    public function save(Role|UserAccount $aggregate): void
    {
        if ($aggregate instanceof Role) {
            $this->saveRole($aggregate);

            return;
        }

        $this->saveUser($aggregate);
    }

    public function existsByEmail(TenantId $tenantId, string $email): bool
    {
        return $this->connection->fetchOne(
            'SELECT 1 FROM wms_user_account WHERE tenant_id = :tenantId AND email = :email',
            ['tenantId' => $tenantId->value(), 'email' => $email],
        ) !== false;
    }

    private function saveRole(Role $role): void
    {
        $this->connection->transactional(static function (Connection $connection) use ($role): void {
            $connection->insert('wms_role', [
                'id' => $role->id()->value(),
                'tenant_id' => $role->tenantId()->value(),
                'code' => $role->code(),
                'name' => $role->name(),
                'created_at' => $role->createdAt()->format('Y-m-d H:i:s.u'),
                'updated_at' => $role->updatedAt()->format('Y-m-d H:i:s.u'),
            ]);

            foreach ($role->permissions() as $permission) {
                $connection->insert('wms_role_permission', [
                    'role_id' => $role->id()->value(),
                    'permission_key' => $permission->value(),
                ]);
            }
        });
    }

    private function saveUser(UserAccount $user): void
    {
        $this->connection->transactional(static function (Connection $connection) use ($user): void {
            $connection->insert('wms_user_account', [
                'id' => $user->id()->value(),
                'tenant_id' => $user->tenantId()->value(),
                'email' => $user->email(),
                'display_name' => $user->displayName(),
                'password_hash' => $user->passwordHash(),
                'status' => $user->status()->value,
                'created_at' => $user->createdAt()->format('Y-m-d H:i:s.u'),
                'updated_at' => $user->updatedAt()->format('Y-m-d H:i:s.u'),
            ]);

            foreach ($user->roles() as $roleId) {
                $connection->insert('wms_user_role', [
                    'user_id' => $user->id()->value(),
                    'role_id' => $roleId->value(),
                ]);
            }
        });
    }
}
