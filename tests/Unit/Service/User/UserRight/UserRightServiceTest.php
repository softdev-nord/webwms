<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\User\UserRight;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserRight;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\User\UserRight\UserRightDataHandler;
use WebWMS\Service\User\UserRight\UserRightService;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service\User\UserRight',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'UserRightServiceTest'
)]
#[CoversClass(UserRightService::class)]
final class UserRightServiceTest extends TestCase
{
    private UserRightService $userRightService;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(UserRightDataHandler::class);

        $this->userRightService = new UserRightService($this->mockObject);
    }

    public function testGetUserRightByUserRightName(): void
    {
        $userRightName = 'create';
        $expectedUserRight = new UserRight();

        $this->mockObject
            ->expects($this->once())
            ->method('getUserRightByUserRightName')
            ->with($userRightName)
            ->willReturn($expectedUserRight);

        $result = $this->userRightService->getUserRightByUserRightName($userRightName);

        self::assertSame($expectedUserRight, $result);
    }

    public function testGetUserRightById(): void
    {
        $userRightId = 1;
        $expectedUserRight = new UserRight();

        $this->mockObject
            ->expects($this->once())
            ->method('getUserRightById')
            ->with($userRightId)
            ->willReturn($expectedUserRight);

        $result = $this->userRightService->getUserRightById($userRightId);

        self::assertSame($expectedUserRight, $result);
    }

    public function testGetAllUserRights(): void
    {
        $expectedUserRights = [new UserRight(), new UserRight()];

        $this->mockObject
            ->expects($this->once())
            ->method('getAllUserRights')
            ->willReturn($expectedUserRights);

        $result = $this->userRightService->getAllUserRights();

        self::assertSame($expectedUserRights, $result);
    }

    public function testAddUserRight(): void
    {
        $request = new Request();

        $this->mockObject
            ->expects($this->once())
            ->method('addUserRight')
            ->with($request);

        $this->userRightService->addUserRight($request);
    }

    public function testUpdateUserRight(): void
    {
        $request = new Request();
        $expectedUserRight = new UserRight();

        $this->mockObject
            ->expects($this->once())
            ->method('updateUserRight')
            ->with($request)
            ->willReturn($expectedUserRight);

        $result = $this->userRightService->updateUserRight($request);

        self::assertSame($expectedUserRight, $result);
    }

    public function testDeleteUserRight(): void
    {
        $userRightName = 'create';

        $this->mockObject
            ->expects($this->once())
            ->method('deleteUserRight')
            ->with($userRightName);

        $this->userRightService->deleteUserRight($userRightName);
    }
}
