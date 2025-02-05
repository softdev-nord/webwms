<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\User;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\User\UserDataHandler;
use WebWMS\Service\User\UserService;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service\User',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'UserServiceTest'
)]
#[CoversClass(UserService::class)]
final class UserServiceTest extends TestCase
{
    private UserService $userService;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(UserDataHandler::class);

        $this->userService = new UserService($this->mockObject);
    }

    public function testGetUserByUsername(): void
    {
        $username = 'rirrgang';
        $expectedUser = new User();

        $this->mockObject
            ->expects($this->once())
            ->method('getUserByUsername')
            ->with($username)
            ->willReturn($expectedUser);

        $result = $this->userService->getUserByUsername($username);

        self::assertSame($expectedUser, $result);
    }

    public function testGetUserById(): void
    {
        $userId = 123;
        $expectedUser = new User();

        $this->mockObject
            ->expects($this->once())
            ->method('getUserById')
            ->with($userId)
            ->willReturn($expectedUser);

        $result = $this->userService->getUserById($userId);

        self::assertSame($expectedUser, $result);
    }

    public function testGetAllUsers(): void
    {
        $jsonResponse = new JsonResponse();

        $this->mockObject
            ->expects($this->once())
            ->method('getAllUsers')
            ->willReturn($jsonResponse);

        $result = $this->userService->getAllUsers();

        self::assertEquals($jsonResponse, $result);
    }

    public function testAddUser(): void
    {
        $request = new Request();

        $this->mockObject
            ->expects($this->once())
            ->method('addUser')
            ->with($request);

        $this->userService->addUser($request);
    }

    public function testGetLastUser(): void
    {
        $expectedUsers = [new User()];

        $this->mockObject
            ->expects($this->once())
            ->method('getLastUser')
            ->willReturn($expectedUsers);

        $result = $this->userService->getLastUser();

        self::assertSame($expectedUsers, $result);
    }

    public function testUpdateUser(): void
    {
        $request = new Request();
        $expectedUser = new User();

        $this->mockObject
            ->expects($this->once())
            ->method('updateUser')
            ->with($request)
            ->willReturn($expectedUser);

        $result = $this->userService->updateUser($request);

        self::assertEquals($expectedUser, $result);
    }

    public function testUpgradePassword(): void
    {
        $user = new User();
        $newHashedPassword = 'newhashedpassword';

        $this->mockObject
            ->expects($this->once())
            ->method('upgradePassword')
            ->with($user, $newHashedPassword);

        $this->userService->upgradePassword($user, $newHashedPassword);
    }

    public function testDeleteUser(): void
    {
        $username = 'rirrgang';

        $this->mockObject
            ->expects($this->once())
            ->method('deleteUser')
            ->with($username);

        $this->userService->deleteUser($username);
    }

    public function testUpdateLastLogin(): void
    {
        $user = new User();

        $this->mockObject
            ->expects($this->once())
            ->method('updateLastLogin')
            ->with($user);

        $this->userService->updateLastLogin($user);
    }
}
