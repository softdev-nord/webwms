<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock\StockLocation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLocationEntity;
use WebWMS\Form\Stock\StockLocation\DeleteStockLocationType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Stock\StockLocationEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        DeleteStockLocationTypeTest
 */
#[CoversClass(DeleteStockLocationType::class)]
final class DeleteStockLocationTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $deleteStockLocationType = new DeleteStockLocationType();
        $deleteStockLocationType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => StockLocationEntity::class]);

        $deleteStockLocationType = new DeleteStockLocationType();
        $deleteStockLocationType->configureOptions($resolver);
    }
}
