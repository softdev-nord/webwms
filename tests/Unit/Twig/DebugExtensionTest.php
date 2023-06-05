<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Twig;

use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;
use WebWMS\Twig\DebugExtension;

/**
 * @package:    WebWMS\Tests\Unit\Twig
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        DebugExtensionTest
 *
 * @covers \WebWMS\Twig\DebugExtension
 */
final class DebugExtensionTest extends TestCase
{
    public function testGetFunctions(): void
    {
        $extension = new DebugExtension();
        $functions = $extension->getFunctions();

        self::assertIsArray($functions);
        self::assertCount(1, $functions);
        self::assertInstanceOf(TwigFunction::class, $functions[0]);
        self::assertEquals('dump', $functions[0]->getName());
        self::assertEquals(['Symfony\Component\VarDumper\VarDumper', 'dump'], $functions[0]->getCallable());
    }

    public function testGetName(): void
    {
        $extension = new DebugExtension();
        $name = $extension->getName();

        self::assertEquals('debug_extension', $name);
    }
}
