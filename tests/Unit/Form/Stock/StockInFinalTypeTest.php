<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Form\Stock\StockInFinalType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\Stock',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'StockInFinalTypeTest'
)]
#[CoversClass(StockInFinalType::class)]
final class StockInFinalTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->willReturnOnConsecutiveCalls(
                ['stock_su_id_', TextType::class, self::anything()],
                ['stock_system', TextType::class, self::anything()],
                ['stock_ln', TextType::class, self::anything()],
                ['stock_fb', TextType::class, self::anything()],
                ['stock_sp', TextType::class, self::anything()],
                ['stock_tf', TextType::class, self::anything()],
                ['stock_quantity', TextType::class, self::anything()],
                ['stock_tbe', TextType::class, self::anything()],
                ['stock_in_post_final', SubmitType::class, self::anything()],
                ['stock_in_correction', ButtonType::class, self::anything()],
                ['stock_in_graphical', ButtonType::class, self::anything()],
                ['back_to_stock_in', ButtonType::class, self::anything()]
            );

        $stockInFinalType = new StockInFinalType();
        $stockInFinalType->buildForm(
            $builder,
            [
                'data' => ['freeStockLocations' => []],
            ]
        );
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with([
                'data_class' => null,
            ]);

        $stockInFinalType = new StockInFinalType();
        $stockInFinalType->configureOptions($resolver);
    }
}
