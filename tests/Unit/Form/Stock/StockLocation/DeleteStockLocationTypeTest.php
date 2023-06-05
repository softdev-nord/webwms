<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock\StockLocation;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLocation;
use WebWMS\Form\Stock\StockLocation\DeleteStockLocationType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Stock\StockLocation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        DeleteStockLocationTypeTest
 *
 * @covers \WebWMS\Form\Stock\StockLocation\DeleteStockLocationType
 */
final class DeleteStockLocationTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->withConsecutive(
                ['stockLocationCoordinate', HiddenType::class, self::anything()],
                ['delete', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $type = new DeleteStockLocationType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => StockLocation::class]);

        $type = new DeleteStockLocationType();
        $type->configureOptions($resolver);
    }
}
