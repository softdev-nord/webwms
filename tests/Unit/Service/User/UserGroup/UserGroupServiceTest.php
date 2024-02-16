<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\User\UserGroup;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserGroupEntity;
use WebWMS\Service\DataHandlers\User\UserGroup\UserGroupDataHandler;
use WebWMS\Service\User\UserGroup\UserGroupService;

/**
 * @package:    WebWMS\Tests\Unit\Service\UserController\UserGroupEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserGroupServiceTest
 */
#[CoversClass(UserGroupService::class)]
final class UserGroupServiceTest extends TestCase
{
    private UserGroupService $userGroupService;

    private MockObject $mockObject;

    #[Override]
    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(UserGroupDataHandler::class);

        $this->userGroupService = new UserGroupService($this->mockObject);
    }

    public function testGetUserGroupByUserGroupName(): void
    {
        $userGroupName = 'Group 1';
        $userGroupEntity = new UserGroupEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('getUserGroupByUserGroupName')
            ->with($userGroupName)
            ->willReturn($userGroupEntity);

        $result = $this->userGroupService->getUserGroupByUserGroupName($userGroupName);

        self::assertSame($userGroupEntity, $result);
    }

    public function testGetUserGroupById(): void
    {
        $userGroupId = 1;
        $userGroupEntity = new UserGroupEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('getUserGroupById')
            ->with($userGroupId)
            ->willReturn($userGroupEntity);

        $result = $this->userGroupService->getUserGroupById($userGroupId);

        self::assertSame($userGroupEntity, $result);
    }

    public function testGetAllUserGroups(): void
    {
        $expectedUserGroups = [new UserGroupEntity(), new UserGroupEntity()];

        $this->mockObject
            ->expects(self::once())
            ->method('getAllUserGroups')
            ->willReturn($expectedUserGroups);

        $result = $this->userGroupService->getAllUserGroups();

        self::assertSame($expectedUserGroups, $result);
    }

    public function testAddUserGroup(): void
    {
        $request = new Request();

        $this->mockObject
            ->expects(self::once())
            ->method('addUserGroup')
            ->with($request);

        $this->userGroupService->addUserGroup($request);
    }

    public function testUpdateUserGroup(): void
    {
        $request = new Request();
        $userGroupEntity = new UserGroupEntity();

        $this->mockObject
            ->expects(self::once())
            ->method('updateUserGroup')
            ->with($request)
            ->willReturn($userGroupEntity);

        $result = $this->userGroupService->updateUserGroup($request);

        self::assertSame($userGroupEntity, $result);
    }

    public function testDeleteUserGroup(): void
    {
        $userGroupName = 'Group 1';

        $this->mockObject
            ->expects(self::once())
            ->method('deleteUserGroup')
            ->with($userGroupName);

        $this->userGroupService->deleteUserGroup($userGroupName);
    }
}
