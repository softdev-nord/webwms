<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\User\UserRole;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserRole;
use WebWMS\Service\DataHandlers\User\UserRole\UserRoleDataHandler;
use WebWMS\Service\User\UserRole\UserRoleService;

/**
 * @package:    WebWMS\Tests\Unit\Service\User\UserRole
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserRoleServiceTest
 *
 * @covers \WebWMS\Service\User\UserRole\UserRoleService
 */
final class UserRoleServiceTest extends TestCase
{
    private UserRoleService $userRoleService;

    private MockObject $userRoleDataHandler;

    protected function setUp(): void
    {
        $this->userRoleDataHandler = $this->createMock(UserRoleDataHandler::class);
        $this->userRoleService = new UserRoleService($this->userRoleDataHandler);
    }

    public function testGetUserRoleByUserRoleName(): void
    {
        $userRoleName = 'ROLE_USER';
        $expectedUserRole = new UserRole();

        $this->userRoleDataHandler
            ->expects(self::once())
            ->method('getUserRoleByUserRoleName')
            ->with($userRoleName)
            ->willReturn($expectedUserRole);

        $result = $this->userRoleService->getUserRoleByUserRoleName($userRoleName);

        self::assertSame($expectedUserRole, $result);
    }

    public function testGetUserRoleById(): void
    {
        $userRoleId = 1;
        $expectedUserRole = new UserRole();

        $this->userRoleDataHandler
            ->expects(self::once())
            ->method('getUserRoleById')
            ->with($userRoleId)
            ->willReturn($expectedUserRole);

        $result = $this->userRoleService->getUserRoleById($userRoleId);

        self::assertSame($expectedUserRole, $result);
    }

    public function testGetAllUserRoles(): void
    {
        $expectedUserRoles = [new UserRole(), new UserRole()];

        $this->userRoleDataHandler
            ->expects(self::once())
            ->method('getAllUserRoles')
            ->willReturn($expectedUserRoles);

        $result = $this->userRoleService->getAllUserRoles();

        self::assertSame($expectedUserRoles, $result);
    }

    public function testAddUserRole(): void
    {
        $request = new Request();

        $this->userRoleDataHandler
            ->expects(self::once())
            ->method('addUserRole')
            ->with($request);

        $this->userRoleService->addUserRole($request);
    }

    public function testUpdateUserRole(): void
    {
        $request = new Request();
        $expectedUserRole = new UserRole();

        $this->userRoleDataHandler
            ->expects(self::once())
            ->method('updateUserRole')
            ->with($request)
            ->willReturn($expectedUserRole);

        $result = $this->userRoleService->updateUserRole($request);

        self::assertSame($expectedUserRole, $result);
    }

    public function testDeleteUserRole(): void
    {
        $userRoleName = 'ROLE_USER';

        $this->userRoleDataHandler
            ->expects(self::once())
            ->method('deleteUserRole')
            ->with($userRoleName);

        $this->userRoleService->deleteUserRole($userRoleName);
    }
}
