<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock\StockLayout;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLayout;
use WebWMS\Form\Stock\StockLayout\AddStockLayoutType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Stock\StockLayout
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        AddStockLayoutTypeTest
 *
 * @covers \WebWMS\Form\Stock\StockLayout\AddStockLayoutType
 */
final class AddStockLayoutTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
        ->method('add')
            ->withConsecutive(
                ['stockNr', TextType::class, self::anything()],
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

        $type = new AddStockLayoutType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => StockLayout::class]);

        $type = new AddStockLayoutType();
        $type->configureOptions($resolver);
    }
}
