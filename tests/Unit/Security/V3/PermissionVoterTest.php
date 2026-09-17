<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Security\V3;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use WebWMS\Security\V3\PermissionVoter;
use WebWMS\Security\V3\SecurityUser;

final class PermissionVoterTest extends TestCase
{
    public function testItGrantsOnlyAssignedPermissions(): void
    {
        $user = new SecurityUser(
            'user-id',
            'tenant-id',
            'admin@example.com',
            'password-hash',
            ['ROLE_ADMIN'],
            ['inventory.stock.read'],
        );
        $token = new UsernamePasswordToken($user, 'v3', $user->getRoles());
        $voter = new PermissionVoter();

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($token, null, ['inventory.stock.read']),
        );
        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($token, null, ['inventory.stock.write']),
        );
        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($token, null, ['ROLE_ADMIN']),
        );
    }
}
