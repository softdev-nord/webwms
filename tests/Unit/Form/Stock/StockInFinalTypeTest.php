<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Form\Stock\StockInFinalType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockInFinalTypeTest
 */
#[CoversClass(StockInFinalType::class)]
final class StockInFinalTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $stockInFinalType = new StockInFinalType();
        $stockInFinalType->buildForm(
            $builder,
            [
                'data' => ['freeStockLocations' => []],
            ]
        );
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with([
                'data_class' => null,
            ]);

        $stockInFinalType = new StockInFinalType();
        $stockInFinalType->configureOptions($resolver);
    }
}
