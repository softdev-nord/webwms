<?php

declare(strict_types=1);

namespace WebWMS\EventSubscriber;

use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @package:    WebWMS\EventSubscriber
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        LocaleSubscriber
 */
class LocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly string $defaultLocale = 'de'
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function onKernelRequest(RequestEvent $requestEvent): void
    {
        $request = $requestEvent->getRequest();
        $locale = $request->getSession()->get('_locale', $this->defaultLocale);
        $request->setLocale((string) $locale);
    }

    #[Override]
    public static function getSubscribedEvents(): array
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
