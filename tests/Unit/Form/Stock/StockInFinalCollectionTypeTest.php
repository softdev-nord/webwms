<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Form\Stock\StockInFinalCollectionType;
use WebWMS\Form\Stock\StockInFinalType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockInFinalCollectionTypeTest
 *
 * @covers \WebWMS\Form\Stock\StockInFinalCollectionType
 */
final class StockInFinalCollectionTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->with('freeStockLocations', CollectionType::class, self::callback(function ($options) {
                return $options['entry_type'] === StockInFinalType::class;
            }));

        $type = new StockInFinalCollectionType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with([
                'data_class' => StockInFinalType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ]);

        $type = new StockInFinalCollectionType();
        $type->configureOptions($resolver);
    }
}
