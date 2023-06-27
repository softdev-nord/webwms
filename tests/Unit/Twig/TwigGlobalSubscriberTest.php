<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Twig;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;
use WebWMS\Service\Configuration\ConfigurationService;
use WebWMS\Twig\TwigGlobalSubscriber;

/**
 * @package:    WebWMS\Tests\Unit\Twig
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TwigGlobalSubscriberTest
 *
 * @covers \WebWMS\Twig\TwigGlobalSubscriber
 */
final class TwigGlobalSubscriberTest extends TestCase
{
    public function testGetSubscribedEventsReturnsCorrectEvent(): void
    {
        $twig = $this->createMock(Environment::class);
        $configurationService = $this->createMock(ConfigurationService::class);

        $twigGlobalSubscriber = new TwigGlobalSubscriber($twig, $configurationService);
        $subscribedEvents = $twigGlobalSubscriber::getSubscribedEvents();

        self::assertCount(1, $subscribedEvents);
        self::assertArrayHasKey(KernelEvents::CONTROLLER, $subscribedEvents);
        self::assertEquals('injectGlobalVariables', $subscribedEvents[KernelEvents::CONTROLLER]);
    }

    /**
     * @throws \Exception
     */
    public function testInjectGlobalVariablesAddsConfigurationsToTwigEnvironment(): void
    {
        $configurations = ['configuration' => ['key1' => 'value1', 'key2' => 'value2']];

        $twig = $this->createMock(Environment::class);
        $configurationService = $this->createMock(ConfigurationService::class);

        $configurationService->expects(self::once())
            ->method('getAllConfigurations')
            ->willReturn($configurations);

        $twig->expects(self::once())
            ->method('addGlobal')
            ->with('configurations', $configurations['configuration']);

        $twigGlobalSubscriber = new TwigGlobalSubscriber($twig, $configurationService);

        $twigGlobalSubscriber->injectGlobalVariables();
    }
}
