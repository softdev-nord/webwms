<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock\StockZone;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockZone;
use WebWMS\Form\Stock\StockZone\EditStockZoneType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\Stock\StockZone',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'EditStockZoneTypeTest'
)]
#[CoversClass(EditStockZoneType::class)]
final class EditStockZoneTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->willReturnOnConsecutiveCalls(
                ['id', HiddenType::class, self::anything()],
                ['stockZoneShortDesc', TextType::class, self::anything()],
                ['stockZoneDescription', TextType::class, self::anything()],
                ['save', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $editStockZoneType = new EditStockZoneType();
        $editStockZoneType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => StockZone::class]);

        $editStockZoneType = new EditStockZoneType();
        $editStockZoneType->configureOptions($resolver);
    }
}
