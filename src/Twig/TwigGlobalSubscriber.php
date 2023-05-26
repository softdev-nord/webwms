<?php

declare(strict_types=1);

namespace WebWMS\Twig;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;
use WebWMS\Service\Configuration\ConfigurationService;

/**
 * @package:    WebWMS\Twig
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TwigGlobalSubscriber
 */
class TwigGlobalSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private ConfigurationService $configurationService
    ) {
    }

    /**
     * @throws \Exception
     */
    public function injectGlobalVariables(): void
    {
        $configurations = $this->configurationService->getAllConfigurations();

        $this->twig->addGlobal('configurations', $configurations['configuration']);
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER => 'injectGlobalVariables'];
    }
}
