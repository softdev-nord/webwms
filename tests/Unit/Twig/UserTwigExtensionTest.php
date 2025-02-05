<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Twig;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;
use Twig\TwigFunction;
use WebWMS\Entity\UserRight;
use WebWMS\Entity\UserRole;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Security\UserRoleRight;
use WebWMS\Twig\UserTwigExtension;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Twig',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'UserTwigExtensionTest'
)]
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
        self::assertSame($this->userTwigExtension->hasUserRole(...), $functions[0]->getCallable());

        self::assertInstanceOf(TwigFunction::class, $functions[1]);
        self::assertSame('has_right', $functions[1]->getName());
        self::assertSame($this->userTwigExtension->hasUserRight(...), $functions[1]->getCallable());

        self::assertInstanceOf(TwigFunction::class, $functions[2]);
        self::assertSame('has_group', $functions[2]->getName());
        self::assertSame($this->userTwigExtension->hasUserGroup(...), $functions[2]->getCallable());
    }

    public function testGetFilters(): void
    {
        $filters = $this->userTwigExtension->getFilters();

        self::assertCount(1, $filters);

        self::assertInstanceOf(TwigFilter::class, $filters[0]);
        self::assertSame('roleHasRight', $filters[0]->getName());
        self::assertSame($this->userTwigExtension->roleHasRight(...), $filters[0]->getCallable());
    }

    public function testHasUserRole(): void
    {
        $userRole = 'view';

        $this->mockObject
            ->expects($this->once())
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
            ->expects($this->once())
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
            ->expects($this->once())
            ->method('hasUserGroup')
            ->with($userGroup)
            ->willReturn(true);

        $result = $this->userTwigExtension->hasUserGroup($userGroup);

        self::assertTrue($result);
    }

    public function testRoleHasRightReturnTrue(): void
    {
        $userRole = new UserRole();
        $userRole->setUserRights(['view']);

        $userRight = new UserRight();
        $userRight->setUserRight('view');

        $result = $this->userTwigExtension->roleHasRight($userRole, $userRight);

        self::assertTrue($result);
    }

    public function testRoleHasRightReturnFalse(): void
    {
        $userRole = new UserRole();
        $userRole->setUserRights(['create']);

        $userRight = new UserRight();
        $userRight->setUserRight('view');

        $result = $this->userTwigExtension->roleHasRight($userRole, $userRight);

        self::assertFalse($result);
    }
}
