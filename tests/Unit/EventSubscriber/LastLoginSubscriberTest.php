<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use WebWMS\Entity\User;
use WebWMS\EventSubscriber\LastLoginSubscriber;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\User\UserService;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\EventSubscriber',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'LastLoginSubscriberTest'
)]
#[CoversClass(LastLoginSubscriber::class)]
final class LastLoginSubscriberTest extends TestCase
{
    public function testUpdateLastLogin(): void
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $userService = $this->createMock(UserService::class);

        $lastLoginSubscriber = new LastLoginSubscriber($tokenStorage, $userService);

        $user = new User();
        $accessToken = $this->createMock(TokenInterface::class);
        $accessToken
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($accessToken);

        $userService->expects($this->once())
            ->method('updateLastLogin')
            ->with($user);

        $lastLoginSubscriber->updateLastLogin();
    }

    public function testGetSubscribedEvents(): void
    {
        $subscribedEvents = LastLoginSubscriber::getSubscribedEvents();

        self::assertArrayHasKey(KernelEvents::FINISH_REQUEST, $subscribedEvents);
        self::assertSame(['updateLastLogin', -10], $subscribedEvents[KernelEvents::FINISH_REQUEST][0]);
    }
}
