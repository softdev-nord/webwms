<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\User;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserEntity;
use WebWMS\Service\DataHandlers\User\UserDataHandler;
use WebWMS\Service\User\UserService;

/**
 * @package:    WebWMS\Tests\Unit\Service\UserController
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserServiceTest
 */
#[CoversClass(UserService::class)]
final class UserServiceTest extends TestCase
{
    private UserService $userService;

    private MockObject $mockObject;

    #[Override]
    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(UserDataHandler::class);

        $this->userService = new UserService($this->mockObject);
    }

    public function testGetUserByUsername(): void
    {
        $username = 'rirrgang';
        $userEntity = new UserEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('getUserByUsername')
            ->with($username)
            ->willReturn($userEntity);

        $result = $this->userService->getUserByUsername($username);

        self::assertSame($userEntity, $result);
    }

    public function testGetUserById(): void
    {
        $userId = 123;
        $userEntity = new UserEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('getUserById')
            ->with($userId)
            ->willReturn($userEntity);

        $result = $this->userService->getUserById($userId);

        self::assertSame($userEntity, $result);
    }

    public function testGetAllUsers(): void
    {
        $jsonResponse = new JsonResponse();

        $this->mockObject
            ->expects(self::once())
            ->method('getAllUsers')
            ->willReturn($jsonResponse);

        $result = $this->userService->getAllUsers();

        self::assertEquals($jsonResponse, $result);
    }

    public function testAddUser(): void
    {
        $request = new Request();

        $this->mockObject
            ->expects(self::once())
            ->method('addUser')
            ->with($request);

        $this->userService->addUser($request);
    }

    public function testGetLastUser(): void
    {
        $expectedUsers = [new UserEntity()];

        $this->mockObject
            ->expects(self::once())
            ->method('getLastUser')
            ->willReturn($expectedUsers);

        $result = $this->userService->getLastUser();

        self::assertSame($expectedUsers, $result);
    }

    public function testUpdateUser(): void
    {
        $request = new Request();
        $userEntity = new UserEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('updateUser')
            ->with($request)
            ->willReturn($userEntity);

        $result = $this->userService->updateUser($request);

        self::assertEquals($userEntity, $result);
    }

    public function testUpgradePassword(): void
    {
        $userEntity = new UserEntity();
        $newHashedPassword = 'newhashedpassword';

        $this->mockObject
            ->expects(self::once())
            ->method('upgradePassword')
            ->with($userEntity, $newHashedPassword);

        $this->userService->upgradePassword($userEntity, $newHashedPassword);
    }

    public function testDeleteUser(): void
    {
        $username = 'rirrgang';

        $this->mockObject
            ->expects(self::once())
            ->method('deleteUser')
            ->with($username);

        $this->userService->deleteUser($username);
    }

    public function testUpdateLastLogin(): void
    {
        $userEntity = new UserEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('updateLastLogin')
            ->with($userEntity);

        $this->userService->updateLastLogin($userEntity);
    }
}
