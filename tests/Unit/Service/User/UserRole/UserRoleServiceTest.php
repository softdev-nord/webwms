<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\User\UserRole;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserRoleEntity;
use WebWMS\Service\DataHandlers\User\UserRole\UserRoleDataHandler;
use WebWMS\Service\User\UserRole\UserRoleService;

/**
 * @package:    WebWMS\Tests\Unit\Service\UserController\UserRoleEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserRoleServiceTest
 */
#[CoversClass(UserRoleService::class)]
final class UserRoleServiceTest extends TestCase
{
    private UserRoleService $userRoleService;

    private MockObject $mockObject;

    #[Override]
    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(UserRoleDataHandler::class);
        $this->userRoleService = new UserRoleService($this->mockObject);
    }

    public function testGetUserRoleByUserRoleName(): void
    {
        $userRoleName = 'ROLE_USER';
        $userRoleEntity = new UserRoleEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('getUserRoleByUserRoleName')
            ->with($userRoleName)
            ->willReturn($userRoleEntity);

        $result = $this->userRoleService->getUserRoleByUserRoleName($userRoleName);

        self::assertSame($userRoleEntity, $result);
    }

    public function testGetUserRoleById(): void
    {
        $userRoleId = 1;
        $userRoleEntity = new UserRoleEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('getUserRoleById')
            ->with($userRoleId)
            ->willReturn($userRoleEntity);

        $result = $this->userRoleService->getUserRoleById($userRoleId);

        self::assertSame($userRoleEntity, $result);
    }

    public function testGetAllUserRoles(): void
    {
        $expectedUserRoles = [new UserRoleEntity(), new UserRoleEntity()];

        $this->mockObject
            ->expects(self::once())
            ->method('getAllUserRoles')
            ->willReturn($expectedUserRoles);

        $result = $this->userRoleService->getAllUserRoles();

        self::assertSame($expectedUserRoles, $result);
    }

    public function testAddUserRole(): void
    {
        $request = new Request();

        $this->mockObject
            ->expects(self::once())
            ->method('addUserRole')
            ->with($request);

        $this->userRoleService->addUserRole($request);
    }

    public function testUpdateUserRole(): void
    {
        $request = new Request();
        $userRoleEntity = new UserRoleEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('updateUserRole')
            ->with($request)
            ->willReturn($userRoleEntity);

        $result = $this->userRoleService->updateUserRole($request);

        self::assertSame($userRoleEntity, $result);
    }

    public function testDeleteUserRole(): void
    {
        $userRoleName = 'ROLE_USER';

        $this->mockObject
            ->expects(self::once())
            ->method('deleteUserRole')
            ->with($userRoleName);

        $this->userRoleService->deleteUserRole($userRoleName);
    }
}
