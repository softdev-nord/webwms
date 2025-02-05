<?php

declare(strict_types=1);

namespace WebWMS\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\EventSubscriber',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'LocaleSubscriber'
)]
readonly class LocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private string $defaultLocale = 'de',
    ) {
    }

    /**
     * @SuppressWarnings(ElseExpression)
     */
    public function onKernelRequest(RequestEvent $requestEvent): void
    {
        $request = $requestEvent->getRequest();
        /** @var string $locale */
        $locale = $request->getSession()->get('_locale', $this->defaultLocale);
        $request->setLocale($locale);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
