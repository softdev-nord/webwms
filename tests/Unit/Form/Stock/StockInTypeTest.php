<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Form\Stock\StockInType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockInTypeTest
 *
 * @covers \WebWMS\Form\Stock\StockInType
 */
final class StockInTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->withConsecutive(
                ['article_id', HiddenType::class, self::anything()],
                ['article_nr', TextType::class, self::anything()],
                ['standard_loading_equipment', ChoiceType::class, self::anything()],
                ['le_quantity', TextType::class, self::anything()],
                ['quantity', TextType::class, self::anything()],
                ['charge', TextType::class, self::anything()],
                ['save', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $type = new StockInType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with([
                'data_class' => null,
            ]);

        $type = new StockInType();
        $type->configureOptions($resolver);
    }
}
