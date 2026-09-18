<?php

declare(strict_types=1);

namespace WebWMS\Security\V3;

use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/** @implements UserProviderInterface<SecurityUser> */
final readonly class DbalUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function loadUserByIdentifier(string $identifier): SecurityUser
    {
        [$tenantId, $email] = $this->splitIdentifier($identifier);
        $rows = $this->connection->fetchAllAssociative(
            'SELECT u.id, u.tenant_id, u.email, u.password_hash, '
            . 'r.code AS role_code, rp.permission_key '
            . 'FROM wms_user_account u '
            . 'LEFT JOIN wms_user_role ur ON ur.user_id = u.id '
            . 'LEFT JOIN wms_role r ON r.id = ur.role_id AND r.tenant_id = u.tenant_id '
            . 'LEFT JOIN wms_role_permission rp ON rp.role_id = r.id '
            . 'WHERE u.tenant_id = :tenantId AND u.email = :email AND u.status = :status',
            ['tenantId' => $tenantId, 'email' => strtolower($email), 'status' => 'active'],
        );

        if ($rows === []) {
            $exception = new UserNotFoundException();
            $exception->setUserIdentifier($identifier);

            throw $exception;
        }

        $first = $rows[0];
        $roles = [];
        $permissions = [];

        foreach ($rows as $row) {
            if (is_string($row['role_code'])) {
                $roles[$row['role_code']] = $row['role_code'];
            }

            if (is_string($row['permission_key'])) {
                $permissions[$row['permission_key']] = $row['permission_key'];
            }
        }

        return new SecurityUser(
            (string) $first['id'],
            (string) $first['tenant_id'],
            (string) $first['email'],
            (string) $first['password_hash'],
            array_values($roles),
            array_values($permissions),
        );
    }

    public function refreshUser(UserInterface $user): SecurityUser
    {
        if (!$user instanceof SecurityUser) {
            throw new UnsupportedUserException(sprintf('Unsupported user class "%s".', $user::class));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return is_a($class, SecurityUser::class, true);
    }

    public function upgradePassword(
        \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface $user,
        string $newHashedPassword,
    ): void {
        if (!$user instanceof SecurityUser) {
            throw new UnsupportedUserException(sprintf('Unsupported user class "%s".', $user::class));
        }

        $this->connection->update(
            'wms_user_account',
            ['password_hash' => $newHashedPassword],
            ['id' => $user->id(), 'tenant_id' => $user->tenantId()],
        );
        $user->replacePasswordHash($newHashedPassword);
    }

    /** @return array{string, string} */
    private function splitIdentifier(string $identifier): array
    {
        $parts = explode('|', $identifier, 2);

        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
            $exception = new UserNotFoundException('The user identifier is invalid.');
            $exception->setUserIdentifier($identifier);

            throw $exception;
        }

        return [$parts[0], $parts[1]];
    }
}
