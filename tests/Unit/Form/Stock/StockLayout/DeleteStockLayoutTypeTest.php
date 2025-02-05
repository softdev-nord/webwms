<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock\StockLayout;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLayout;
use WebWMS\Form\Stock\StockLayout\DeleteStockLayoutType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\Stock\StockLayout',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'DeleteStockLayoutTypeTest'
)]
#[CoversClass(DeleteStockLayoutType::class)]
final class DeleteStockLayoutTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->willReturnOnConsecutiveCalls(
                ['id', HiddenType::class, self::anything()],
                ['delete', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $deleteStockLayoutType = new DeleteStockLayoutType();
        $deleteStockLayoutType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => StockLayout::class]);

        $deleteStockLayoutType = new DeleteStockLayoutType();
        $deleteStockLayoutType->configureOptions($resolver);
    }
}
