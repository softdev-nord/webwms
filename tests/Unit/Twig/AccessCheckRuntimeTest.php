<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Twig;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Twig\AccessCheckRuntime;

/**
 * @package:    WebWMS\Tests\Unit\Twig
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        AccessCheckRuntimeTest
 *
 * @covers \WebWMS\Twig\AccessCheckRuntime
 */
final class AccessCheckRuntimeTest extends TestCase
{
    public function testHasRoleReturnsTrueWhenUserHasRole(): void
    {
        $user = $this->createMock(UserInterface::class);
        $roleHierarchy = $this->createMock(RoleHierarchyInterface::class);

        $roleHierarchy->expects(self::once())
            ->method('getReachableRoleNames')
            ->with($user->getRoles())
            ->willReturn(['ROLE_ADMIN', 'ROLE_USER']);

        $accessCheckRuntime = new AccessCheckRuntime($roleHierarchy);

        self::assertTrue($accessCheckRuntime->hasRole($user, 'ROLE_ADMIN'));
    }

    public function testHasRoleReturnsFalseWhenUserDoesNotHaveRole(): void
    {
        $user = $this->createMock(UserInterface::class);
        $roleHierarchy = $this->createMock(RoleHierarchyInterface::class);

        $roleHierarchy->expects(self::once())
            ->method('getReachableRoleNames')
            ->with($user->getRoles())
            ->willReturn(['ROLE_USER']);

        $accessCheckRuntime = new AccessCheckRuntime($roleHierarchy);

        self::assertFalse($accessCheckRuntime->hasRole($user, 'ROLE_ADMIN'));
    }
}
