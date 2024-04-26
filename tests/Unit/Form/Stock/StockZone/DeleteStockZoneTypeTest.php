<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock\StockZone;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockZoneEntity;
use WebWMS\Form\Stock\StockZone\DeleteStockZoneType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Stock\StockZoneEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        DeleteStockZoneTypeTest
 */
#[CoversClass(DeleteStockZoneType::class)]
final class DeleteStockZoneTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $deleteStockZoneType = new DeleteStockZoneType();
        $deleteStockZoneType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => StockZoneEntity::class]);

        $deleteStockZoneType = new DeleteStockZoneType();
        $deleteStockZoneType->configureOptions($resolver);
    }
}
