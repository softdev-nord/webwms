<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Twig;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Twig\VarsExtension;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Twig',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'VarsExtensionTest'
)]
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
        self::assertSame('json_decode', $filter->getName());
        self::assertEquals($varsExtension->jsonDecode(...), $filter->getCallable());
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

        self::assertSame($expectedResult, $result);
    }
}
