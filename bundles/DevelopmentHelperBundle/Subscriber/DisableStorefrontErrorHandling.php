<?php

declare(strict_types=1);

namespace WebWMS\Bundles\DevelopmentHelperBundle\Subscriber;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @package:    WebWMS\Bundles\DevelopmentHelperBundle\Subscriber
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        DisableStorefrontErrorHandling
 */
class DisableStorefrontErrorHandling implements EventSubscriberInterface
{
    public function __construct(
        private ContainerBagInterface $containerBag
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['disableFrontendErrorHandling', -95],
        ];
    }

    public function disableFrontendErrorHandling(ExceptionEvent $event)
    {
        // if we are in dev mode, we will see exceptions
        if ($this->containerBag->all()['kernel.debug']) {
            return;
        }
    }
}
