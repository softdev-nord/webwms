<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Twig;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;
use Twig\TwigFunction;
use WebWMS\Entity\UserRightEntity;
use WebWMS\Entity\UserRoleEntity;
use WebWMS\Security\UserRoleRight;
use WebWMS\Twig\UserTwigExtension;

/**
 * @package:    WebWMS\Tests\Unit\Twig
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        UserTwigExtensionTest
 */
#[CoversClass(UserTwigExtension::class)]
final class UserTwigExtensionTest extends TestCase
{
    private UserTwigExtension $userTwigExtension;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(UserRoleRight::class);
        $this->userTwigExtension = new UserTwigExtension($this->mockObject);
    }

    public function testGetFunctions(): void
    {
        $functions = $this->userTwigExtension->getFunctions();

        self::assertCount(3, $functions);

        self::assertInstanceOf(TwigFunction::class, $functions[0]);
        self::assertSame('has_role', $functions[0]->getName());

        self::assertInstanceOf(TwigFunction::class, $functions[1]);
        self::assertSame('has_right', $functions[1]->getName());

        self::assertInstanceOf(TwigFunction::class, $functions[2]);
        self::assertSame('has_group', $functions[2]->getName());
    }

    public function testGetFilters(): void
    {
        $filters = $this->userTwigExtension->getFilters();

        self::assertCount(1, $filters);

        self::assertInstanceOf(TwigFilter::class, $filters[0]);
        self::assertSame('roleHasRight', $filters[0]->getName());
    }

    public function testHasUserRole(): void
    {
        $userRole = 'view';

        $this->mockObject
            ->expects(self::once())
            ->method('hasUserRole')
            ->with($userRole)
            ->willReturn(true);

        $result = $this->userTwigExtension->hasUserRole($userRole);

        self::assertTrue($result);
    }

    public function testHasUserRight(): void
    {
        $userRight = 'view';

        $this->mockObject
            ->expects(self::once())
            ->method('hasUserRight')
            ->with($userRight)
            ->willReturn(true);

        $result = $this->userTwigExtension->hasUserRight($userRight);

        self::assertTrue($result);
    }

    public function testHasUserGroup(): void
    {
        $userGroup = 'GROUP_SUPER_ADMIN';

        $this->mockObject
            ->expects(self::once())
            ->method('hasUserGroup')
            ->with($userGroup)
            ->willReturn(true);

        $result = $this->userTwigExtension->hasUserGroup($userGroup);

        self::assertTrue($result);
    }

    public function testRoleHasRightReturnTrue(): void
    {
        $userRoleEntity = new UserRoleEntity();
        $userRoleEntity->setUserRights(['view']);

        $userRightEntity = new UserRightEntity();
        $userRightEntity->setUserRight('view');

        $result = $this->userTwigExtension->roleHasRight($userRoleEntity, $userRightEntity);

        self::assertTrue($result);
    }

    public function testRoleHasRightReturnFalse(): void
    {
        $userRoleEntity = new UserRoleEntity();
        $userRoleEntity->setUserRights(['create']);

        $userRightEntity = new UserRightEntity();
        $userRightEntity->setUserRight('view');

        $result = $this->userTwigExtension->roleHasRight($userRoleEntity, $userRightEntity);

        self::assertFalse($result);
    }
}
