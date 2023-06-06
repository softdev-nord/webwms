<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use WebWMS\EventSubscriber\LocaleSubscriber;

/**
 * @package:    WebWMS\Tests\Unit\EventSubscriber
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        LocaleSubscriberTest
 *
 * @covers \WebWMS\EventSubscriber\LocaleSubscriber
 */
final class LocaleSubscriberTest extends TestCase
{
    public function testOnKernelRequestWithSessionLocale(): void
    {
        // Create mock objects for dependencies
        $request = $this->createMock(Request::class);
        $session = $this->createMock(SessionInterface::class);
        $event = $this->createMock(RequestEvent::class);

        $request->expects($this->once())
            ->method('getSession')
            ->willReturn($session);

        $session->expects($this->once())
            ->method('get')
            ->with($this->equalTo('_locale'), $this->equalTo('de'))
            ->willReturn('fr');

        $request->expects($this->once())
            ->method('setLocale')
            ->with($this->equalTo('fr'));

        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        // Create an instance of LocaleSubscriber and call the method being tested
        $subscriber = new LocaleSubscriber();
        $subscriber->onKernelRequest($event);
    }
//    public function testOnKernelRequest(): void
//    {
//        $defaultLocale = 'de';
//        $request = Request::create('/');
//        $requestStack = new RequestStack();
//        $requestStack->push($request);
//        $httpKernelInterface = $this->createMock(HttpKernelInterface::class);
//
//        $session = $this->createMock(SessionInterface::class);
//        $session->expects($this->once())
//            ->method('get')
//            ->with('_locale', $defaultLocale)
//            ->willReturn('en');
//
//        $requestEvent = new RequestEvent($httpKernelInterface, $request, null, $requestStack);
//        $requestEvent->setRequest($request);
//
//        $subscriber = new LocaleSubscriber($defaultLocale);
//        $subscriber->onKernelRequest($requestEvent);
//
//        $this->assertSame('en', $request->getLocale());
//    }

    public function testGetSubscribedEvents(): void
    {
        $subscriber = new LocaleSubscriber();

        $subscribedEvents = $subscriber->getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::REQUEST, $subscribedEvents);
        $this->assertSame([['onKernelRequest', 20]], $subscribedEvents[KernelEvents::REQUEST]);
    }
}
