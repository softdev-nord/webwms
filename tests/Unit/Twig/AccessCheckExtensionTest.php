<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Twig;

use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;
use Twig\TwigFunction;
use WebWMS\Twig\AccessCheckExtension;
use WebWMS\Twig\AccessCheckRuntime;

/**
 * @package:    WebWMS\Tests\Unit\Twig
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        AccessCheckExtensionTest
 *
 * @covers \WebWMS\Twig\AccessCheckExtension
 */
final class AccessCheckExtensionTest extends TestCase
{
    public function testGetFiltersReturnsCorrectTwigFilters(): void
    {
        $accessCheckExtension = new AccessCheckExtension();
        $filters = $accessCheckExtension->getFilters();

        self::assertCount(1, $filters);

        $expectedFilter = new TwigFilter('has_role', [AccessCheckRuntime::class, 'hasRole']);
        self::assertEquals($expectedFilter, $filters[0]);
    }

    public function testGetFunctionsReturnsCorrectTwigFunctions(): void
    {
        $accessCheckExtension = new AccessCheckExtension();
        $functions = $accessCheckExtension->getFunctions();

        self::assertCount(1, $functions);

        $expectedFunction = new TwigFunction('has_role', [AccessCheckRuntime::class, 'hasRole']);
        self::assertEquals($expectedFunction, $functions[0]);
    }
}
