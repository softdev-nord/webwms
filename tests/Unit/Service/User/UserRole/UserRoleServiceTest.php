<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\User\UserRole;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserRole;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\User\UserRole\UserRoleDataHandler;
use WebWMS\Service\User\UserRole\UserRoleService;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service\User\UserRole',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'UserRoleServiceTest'
)]
#[CoversClass(UserRoleService::class)]
final class UserRoleServiceTest extends TestCase
{
    private UserRoleService $userRoleService;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(UserRoleDataHandler::class);
        $this->userRoleService = new UserRoleService($this->mockObject);
    }

    public function testGetUserRoleByUserRoleName(): void
    {
        $userRoleName = 'ROLE_USER';
        $expectedUserRole = new UserRole();

        $this->mockObject
            ->expects($this->once())
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

        $this->mockObject
            ->expects($this->once())
            ->method('getUserRoleById')
            ->with($userRoleId)
            ->willReturn($expectedUserRole);

        $result = $this->userRoleService->getUserRoleById($userRoleId);

        self::assertSame($expectedUserRole, $result);
    }

    public function testGetAllUserRoles(): void
    {
        $expectedUserRoles = [new UserRole(), new UserRole()];

        $this->mockObject
            ->expects($this->once())
            ->method('getAllUserRoles')
            ->willReturn($expectedUserRoles);

        $result = $this->userRoleService->getAllUserRoles();

        self::assertSame($expectedUserRoles, $result);
    }

    public function testAddUserRole(): void
    {
        $request = new Request();

        $this->mockObject
            ->expects($this->once())
            ->method('addUserRole')
            ->with($request);

        $this->userRoleService->addUserRole($request);
    }

    public function testUpdateUserRole(): void
    {
        $request = new Request();
        $expectedUserRole = new UserRole();

        $this->mockObject
            ->expects($this->once())
            ->method('updateUserRole')
            ->with($request)
            ->willReturn($expectedUserRole);

        $result = $this->userRoleService->updateUserRole($request);

        self::assertSame($expectedUserRole, $result);
    }

    public function testDeleteUserRole(): void
    {
        $userRoleName = 'ROLE_USER';

        $this->mockObject
            ->expects($this->once())
            ->method('deleteUserRole')
            ->with($userRoleName);

        $this->userRoleService->deleteUserRole($userRoleName);
    }
}
