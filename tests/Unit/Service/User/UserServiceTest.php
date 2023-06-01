<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\User;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\User;
use WebWMS\Service\DataHandlers\User\UserDataHandler;
use WebWMS\Service\User\UserService;

/**
 * @package:    WebWMS\Tests\Unit\Service\User
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserServiceTest
 *
 * @covers \WebWMS\Service\User\UserService
 */
final class UserServiceTest extends TestCase
{
    private UserService $userService;

    /**
     * @var (UserDataHandler&MockObject)|MockObject
     */
    private MockObject|UserDataHandler $userDataHandler;

    protected function setUp(): void
    {
        $this->userDataHandler = $this->createMock(UserDataHandler::class);

        $this->userService = new UserService($this->userDataHandler);
    }

    public function testGetUserByUsername(): void
    {
        $username = 'rirrgang';
        $expectedUser = new User();

        $this->userDataHandler
            ->expects(self::once())
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

        $this->userDataHandler
            ->expects(self::once())
            ->method('getUserById')
            ->with($userId)
            ->willReturn($expectedUser);

        $result = $this->userService->getUserById($userId);

        self::assertSame($expectedUser, $result);
    }

    public function testGetAllUsers(): void
    {
        $expectedResponse = new JsonResponse();

        $this->userDataHandler
            ->expects(self::once())
            ->method('getAllUsers')
            ->willReturn($expectedResponse);

        $result = $this->userService->getAllUsers();

        self::assertEquals($expectedResponse, $result);
    }

    public function testAddUser(): void
    {
        $request = new Request();

        $this->userDataHandler
            ->expects(self::once())
            ->method('addUser')
            ->with($request);

        $this->userService->addUser($request);
    }

    public function testGetLastUser(): void
    {
        $expectedUsers = [new User()];

        $this->userDataHandler
            ->expects(self::once())
            ->method('getLastUser')
            ->willReturn($expectedUsers);

        $result = $this->userService->getLastUser();

        self::assertSame($expectedUsers, $result);
    }

    public function testUpdateUser(): void
    {
        $request = new Request();
        $expectedUser = new User();

        $this->userDataHandler
            ->expects(self::once())
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

        $this->userDataHandler
            ->expects(self::once())
            ->method('upgradePassword')
            ->with($user, $newHashedPassword);

        $this->userService->upgradePassword($user, $newHashedPassword);
    }

    public function testDeleteUser(): void
    {
        $username = 'rirrgang';

        $this->userDataHandler
            ->expects(self::once())
            ->method('deleteUser')
            ->with($username);

        $this->userService->deleteUser($username);
    }

    public function testUpdateLastLogin(): void
    {
        $user = new User();

        $this->userDataHandler
            ->expects(self::once())
            ->method('updateLastLogin')
            ->with($user);

        $this->userService->updateLastLogin($user);
    }
}
