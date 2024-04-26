<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Twig;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\VarDumper\VarDumper;
use Twig\TwigFunction;
use WebWMS\Twig\DebugExtension;

/**
 * @package:    WebWMS\Tests\Unit\Twig
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        DebugExtensionTest
 */
#[CoversClass(DebugExtension::class)]
final class DebugExtensionTest extends TestCase
{
    public function testGetFunctions(): void
    {
        $debugExtension = new DebugExtension();
        $functions = $debugExtension->getFunctions();

        self::assertIsArray($functions);
        self::assertCount(1, $functions);
        self::assertInstanceOf(TwigFunction::class, $functions[0]);
        self::assertEquals('dump', $functions[0]->getName());
        self::assertEquals(static fn ($var, ?string $label = null): mixed => VarDumper::dump($var, $label), $functions[0]->getCallable());
    }

    public function testGetName(): void
    {
        $debugExtension = new DebugExtension();
        $name = $debugExtension->getName();

        self::assertEquals('debug_extension', $name);
    }
}
