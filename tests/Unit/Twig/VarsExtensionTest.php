<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Twig;

use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;
use WebWMS\Twig\VarsExtension;

/**
 * @package:    WebWMS\Tests\Unit\Twig
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        VarsExtensionTest
 *
 * @covers \WebWMS\Twig\VarsExtension
 */
final class VarsExtensionTest extends TestCase
{
    public function testGetFiltersMethod(): void
    {
        $extension = new VarsExtension();
        $filters = $extension->getFilters();

        self::assertIsArray($filters);
        self::assertCount(1, $filters);

        $filter = $filters[0];
        self::assertInstanceOf(TwigFilter::class, $filter);
        self::assertEquals('json_decode', $filter->getName());
        self::assertEquals([$extension, 'jsonDecode'], $filter->getCallable());
    }

    public function testJsonDecode(): void
    {
        $extension = new VarsExtension();
        $str = '{"name": "John", "age": 30}';
        $expectedResult = json_decode($str);

        $result = $extension->jsonDecode($str);

        self::assertEquals($expectedResult, $result);
    }

    public function testGetNameMethod(): void
    {
        $extension = new VarsExtension();
        $expectedResult = 'vars_extension';

        $result = $extension->getName();

        self::assertEquals($expectedResult, $result);
    }
}
