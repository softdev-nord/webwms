<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use WebWMS\Entity\User;
use WebWMS\EventSubscriber\LastLoginSubscriber;
use WebWMS\Service\User\UserService;

/**
 * @package:    WebWMS\Tests\Unit\EventSubscriber
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        LastLoginSubscriberTest
 *
 * @covers \WebWMS\EventSubscriber\LastLoginSubscriber
 */
final class LastLoginSubscriberTest extends TestCase
{
    public function testUpdateLastLogin(): void
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $userService = $this->createMock(UserService::class);

        $subscriber = new LastLoginSubscriber($tokenStorage, $userService);

        $user = new User();
        $accessToken = $this->createMock(TokenInterface::class);
        $accessToken
            ->expects(self::once())
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage
            ->expects(self::once())
            ->method('getToken')
            ->willReturn($accessToken);

        $userService->expects(self::once())
            ->method('updateLastLogin')
            ->with($user);

        $subscriber->updateLastLogin();
    }

    public function testGetSubscribedEvents(): void
    {
        $subscriber = new LastLoginSubscriber(
            $this->createMock(TokenStorageInterface::class),
            $this->createMock(UserService::class)
        );

        $subscribedEvents = LastLoginSubscriber::getSubscribedEvents();

        self::assertArrayHasKey(KernelEvents::FINISH_REQUEST, $subscribedEvents);
        self::assertSame(['updateLastLogin', -10], $subscribedEvents[KernelEvents::FINISH_REQUEST][0]);
    }
}
