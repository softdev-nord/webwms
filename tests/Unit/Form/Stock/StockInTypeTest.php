<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Form\Stock\StockInType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        StockInTypeTest
 */
#[CoversClass(StockInType::class)]
final class StockInTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $stockInType = new StockInType();
        $stockInType->buildForm($builder, []);
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

        $stockInType = new StockInType();
        $stockInType->configureOptions($resolver);
    }
}
