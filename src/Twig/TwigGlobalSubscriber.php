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
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        TwigGlobalSubscriber
 */
class TwigGlobalSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private ConfigurationService $configurationService
    ) {
    }

    public function injectGlobalVariables(): void
    {
        $configurations = $this->configurationService->getAllConfigurations();
        $this->twig->addGlobal('configurations', $configurations['configuration']);
    }

    /*public function injectGlobalVariables()
    {
        $base_params = $this->configurationService->getAllConfigurations();
        $this->twig->addGlobal('config', $base_params);
        foreach ($base_params as $key => $value) {
            $this->twig->addGlobal('config', $base_params);
        }
    }*/

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER => 'injectGlobalVariables'];
    }

    public function onKernelRequest(): void
    {
    }
}
