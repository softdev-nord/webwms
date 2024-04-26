<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock\StockZone;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockZoneEntity;
use WebWMS\Form\Stock\StockZone\EditStockZoneType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Stock\StockZoneEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        EditStockZoneTypeTest
 */
#[CoversClass(EditStockZoneType::class)]
final class EditStockZoneTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $editStockZoneType = new EditStockZoneType();
        $editStockZoneType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => StockZoneEntity::class]);

        $editStockZoneType = new EditStockZoneType();
        $editStockZoneType->configureOptions($resolver);
    }
}
