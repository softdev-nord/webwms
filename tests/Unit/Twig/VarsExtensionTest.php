<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Twig;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;
use WebWMS\Twig\VarsExtension;

/**
 * @package:    WebWMS\Tests\Unit\Twig
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        VarsExtensionTest
 */
#[CoversClass(VarsExtension::class)]
final class VarsExtensionTest extends TestCase
{
    public function testGetFiltersMethod(): void
    {
        $varsExtension = new VarsExtension();
        $filters = $varsExtension->getFilters();

        self::assertIsArray($filters);
        self::assertCount(1, $filters);

        $filter = $filters[0];
        self::assertInstanceOf(TwigFilter::class, $filter);
        self::assertEquals('json_decode', $filter->getName());
        self::assertEquals(static fn (string $str): mixed => $varsExtension->jsonDecode($str), $filter->getCallable());
    }

    public function testJsonDecode(): void
    {
        $varsExtension = new VarsExtension();
        $str = '{"name": "John", "age": 30}';
        $expectedResult = json_decode($str);

        $result = $varsExtension->jsonDecode($str);

        self::assertEquals($expectedResult, $result);
    }

    public function testGetNameMethod(): void
    {
        $varsExtension = new VarsExtension();
        $expectedResult = 'vars_extension';

        $result = $varsExtension->getName();

        self::assertEquals($expectedResult, $result);
    }
}
