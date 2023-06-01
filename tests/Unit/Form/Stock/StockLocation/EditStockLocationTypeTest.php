<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock\StockLocation;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLocation;
use WebWMS\Form\Stock\StockLocation\EditStockLocationType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Stock\StockLocation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        EditStockLocationTypeTest
 *
 * @covers \WebWMS\Form\Stock\StockLocation\EditStockLocationType
 */
final class EditStockLocationTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->withConsecutive(
                ['stockLocationLn', TextType::class, self::anything()],
                ['stockDescription', TextType::class, self::anything()],
                ['stockLevel1', TextType::class, self::anything()],
                ['stockLevel2', TextType::class, self::anything()],
                ['stockLevel3', TextType::class, self::anything()],
                ['stockLevel4', TextType::class, self::anything()],
                ['stockModel', TextType::class, self::anything()],
                ['stockTyp', TextType::class, self::anything()],
                ['stockLongDescription', TextType::class, self::anything()],
                ['save', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $type = new EditStockLocationType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => StockLocation::class]);

        $type = new EditStockLocationType();
        $type->configureOptions($resolver);
    }
}
