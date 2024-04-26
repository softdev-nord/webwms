<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\User\UserRight;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserRightEntity;
use WebWMS\Service\DataHandlers\User\UserRight\UserRightDataHandler;
use WebWMS\Service\User\UserRight\UserRightService;

/**
 * @package:    WebWMS\Tests\Unit\Service\UserController\UserRightEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        UserRightServiceTest
 */
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
        $userRightEntity = new UserRightEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('getUserRightByUserRightName')
            ->with($userRightName)
            ->willReturn($userRightEntity);

        $result = $this->userRightService->getUserRightByUserRightName($userRightName);

        self::assertSame($userRightEntity, $result);
    }

    public function testGetUserRightById(): void
    {
        $userRightId = 1;
        $userRightEntity = new UserRightEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('getUserRightById')
            ->with($userRightId)
            ->willReturn($userRightEntity);

        $result = $this->userRightService->getUserRightById($userRightId);

        self::assertSame($userRightEntity, $result);
    }

    public function testGetAllUserRights(): void
    {
        $expectedUserRights = [new UserRightEntity(), new UserRightEntity()];

        $this->mockObject
            ->expects(self::once())
            ->method('getAllUserRights')
            ->willReturn($expectedUserRights);

        $result = $this->userRightService->getAllUserRights();

        self::assertSame($expectedUserRights, $result);
    }

    public function testAddUserRight(): void
    {
        $request = new Request();

        $this->mockObject
            ->expects(self::once())
            ->method('addUserRight')
            ->with($request);

        $this->userRightService->addUserRight($request);
    }

    public function testUpdateUserRight(): void
    {
        $request = new Request();
        $userRightEntity = new UserRightEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('updateUserRight')
            ->with($request)
            ->willReturn($userRightEntity);

        $result = $this->userRightService->updateUserRight($request);

        self::assertSame($userRightEntity, $result);
    }

    public function testDeleteUserRight(): void
    {
        $userRightName = 'create';

        $this->mockObject
            ->expects(self::once())
            ->method('deleteUserRight')
            ->with($userRightName);

        $this->userRightService->deleteUserRight($userRightName);
    }
}
