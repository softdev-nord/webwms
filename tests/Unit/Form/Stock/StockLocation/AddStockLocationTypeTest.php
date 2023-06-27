<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock\StockLocation;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLocation;
use WebWMS\Form\Stock\StockLocation\AddStockLocationType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Stock\StockLocation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        AddStockLocationTypeTest
 *
 * @covers \WebWMS\Form\Stock\StockLocation\AddStockLocationType
 */
final class AddStockLocationTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->withConsecutive(
                ['stockLocationLn', TextType::class, self::anything()],
                ['stockLocationFb', TextType::class, self::anything()],
                ['stockLocationSp', TextType::class, self::anything()],
                ['stockLocationTf', TextType::class, self::anything()],
                ['stockLocationDesc', TextType::class, self::anything()],
                ['stockLocationWidth', TextType::class, self::anything()],
                ['stockLocationDepth', TextType::class, self::anything()],
                ['stockLocationHeight', TextType::class, self::anything()],
                ['stockLocationZone', ChoiceType::class, self::anything()],
                ['stock_location_check', CheckboxType::class, self::anything()],
                ['save', SubmitType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $type = new AddStockLocationType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => StockLocation::class]);

        $type = new AddStockLocationType();
        $type->configureOptions($resolver);
    }
}
