<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\User\UserGroup;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserGroup;
use WebWMS\Service\DataHandlers\User\UserGroup\UserGroupDataHandler;
use WebWMS\Service\User\UserGroup\UserGroupService;

/**
 * @package:    WebWMS\Tests\Unit\Service\User\UserGroup
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserGroupServiceTest
 *
 * @covers \WebWMS\Service\User\UserGroup\UserGroupService
 */
final class UserGroupServiceTest extends TestCase
{
    private UserGroupService $userGroupService;

    private MockObject $userGroupDataHandler;

    protected function setUp(): void
    {
        $this->userGroupDataHandler = $this->createMock(UserGroupDataHandler::class);

        $this->userGroupService = new UserGroupService($this->userGroupDataHandler);
    }

    public function testGetUserGroupByUserGroupName(): void
    {
        $userGroupName = 'Group 1';
        $expectedUserGroup = new UserGroup();

        $this->userGroupDataHandler
            ->expects(self::once())
            ->method('getUserGroupByUserGroupName')
            ->with($userGroupName)
            ->willReturn($expectedUserGroup);

        $result = $this->userGroupService->getUserGroupByUserGroupName($userGroupName);

        self::assertSame($expectedUserGroup, $result);
    }

    public function testGetUserGroupById(): void
    {
        $userGroupId = 1;
        $expectedUserGroup = new UserGroup();

        $this->userGroupDataHandler
            ->expects(self::once())
            ->method('getUserGroupById')
            ->with($userGroupId)
            ->willReturn($expectedUserGroup);

        $result = $this->userGroupService->getUserGroupById($userGroupId);

        self::assertSame($expectedUserGroup, $result);
    }

    public function testGetAllUserGroups(): void
    {
        $expectedUserGroups = [new UserGroup(), new UserGroup()];

        $this->userGroupDataHandler
            ->expects(self::once())
            ->method('getAllUserGroups')
            ->willReturn($expectedUserGroups);

        $result = $this->userGroupService->getAllUserGroups();

        self::assertSame($expectedUserGroups, $result);
    }

    public function testAddUserGroup(): void
    {
        $request = new Request();

        $this->userGroupDataHandler
            ->expects(self::once())
            ->method('addUserGroup')
            ->with($request);

        $this->userGroupService->addUserGroup($request);
    }

    public function testUpdateUserGroup(): void
    {
        $request = new Request();
        $expectedUserGroup = new UserGroup();

        $this->userGroupDataHandler
            ->expects(self::once())
            ->method('updateUserGroup')
            ->with($request)
            ->willReturn($expectedUserGroup);

        $result = $this->userGroupService->updateUserGroup($request);

        self::assertSame($expectedUserGroup, $result);
    }

    public function testDeleteUserGroup(): void
    {
        $userGroupName = 'Group 1';

        $this->userGroupDataHandler
            ->expects(self::once())
            ->method('deleteUserGroup')
            ->with($userGroupName);

        $this->userGroupService->deleteUserGroup($userGroupName);
    }
}
