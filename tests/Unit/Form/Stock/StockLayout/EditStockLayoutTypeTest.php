<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock\StockLayout;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLayout;
use WebWMS\Form\Stock\StockLayout\EditStockLayoutType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\Stock\StockLayout',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'EditStockLayoutTypeTest'
)]
#[CoversClass(EditStockLayoutType::class)]
final class EditStockLayoutTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->willReturnOnConsecutiveCalls(
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

        $editStockLayoutType = new EditStockLayoutType();
        $editStockLayoutType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => StockLayout::class]);

        $editStockLayoutType = new EditStockLayoutType();
        $editStockLayoutType->configureOptions($resolver);
    }
}
