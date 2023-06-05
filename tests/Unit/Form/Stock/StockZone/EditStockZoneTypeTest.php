<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock\StockZone;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockZone;
use WebWMS\Form\Stock\StockZone\EditStockZoneType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Stock\StockZone
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        EditStockZoneTypeTest
 *
 * @covers \WebWMS\Form\Stock\StockZone\EditStockZoneType
 */
final class EditStockZoneTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->withConsecutive(
                ['id', HiddenType::class, self::anything()],
                ['stockZoneShortDesc', TextType::class, self::anything()],
                ['stockZoneDescription', TextType::class, self::anything()],
                ['save', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $type = new EditStockZoneType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => StockZone::class]);

        $type = new EditStockZoneType();
        $type->configureOptions($resolver);
    }
}
