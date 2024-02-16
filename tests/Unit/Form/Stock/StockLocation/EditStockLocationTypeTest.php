<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock\StockLocation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLocationEntity;
use WebWMS\Form\Stock\StockLocation\EditStockLocationType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Stock\StockLocationEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        EditStockLocationTypeTest
 */
#[CoversClass(EditStockLocationType::class)]
final class EditStockLocationTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $editStockLocationType = new EditStockLocationType();
        $editStockLocationType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => StockLocationEntity::class]);

        $editStockLocationType = new EditStockLocationType();
        $editStockLocationType->configureOptions($resolver);
    }
}
