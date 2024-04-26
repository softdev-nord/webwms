<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use WebWMS\EventSubscriber\LocaleSubscriber;

/**
 * @package:    WebWMS\Tests\Unit\EventSubscriber
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        LocaleSubscriberTest
 */
#[CoversClass(LocaleSubscriber::class)]
final class LocaleSubscriberTest extends TestCase
{
    public function testOnKernelRequestWithSessionLocale(): void
    {
        // Create mock objects for dependencies
        $request = $this->createMock(Request::class);
        $session = $this->createMock(SessionInterface::class);
        $event = $this->createMock(RequestEvent::class);

        $request
            ->expects(self::once())
            ->method('getSession')
            ->willReturn($session);

        $session
            ->expects(self::once())
            ->method('get')
            ->with(self::equalTo('_locale'), self::equalTo('de'))
            ->willReturn('fr');

        $request
            ->expects(self::once())
            ->method('setLocale')
            ->with(self::equalTo('fr'));

        $event
            ->expects(self::once())
            ->method('getRequest')
            ->willReturn($request);

        $localeSubscriber = new LocaleSubscriber();
        $localeSubscriber->onKernelRequest($event);
    }

    public function testGetSubscribedEvents(): void
    {
        $subscribedEvents = LocaleSubscriber::getSubscribedEvents();

        self::assertArrayHasKey(KernelEvents::REQUEST, $subscribedEvents);
        self::assertSame([['onKernelRequest', 20]], $subscribedEvents[KernelEvents::REQUEST]);
    }
}
