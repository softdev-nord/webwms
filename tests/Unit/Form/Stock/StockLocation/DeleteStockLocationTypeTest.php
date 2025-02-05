<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock\StockLocation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLocation;
use WebWMS\Form\Stock\StockLocation\DeleteStockLocationType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\Stock\StockLocation',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'DeleteStockLocationTypeTest'
)]
#[CoversClass(DeleteStockLocationType::class)]
final class DeleteStockLocationTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->willReturnOnConsecutiveCalls(
                ['stockLocationCoordinate', HiddenType::class, self::anything()],
                ['delete', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $deleteStockLocationType = new DeleteStockLocationType();
        $deleteStockLocationType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => StockLocation::class]);

        $deleteStockLocationType = new DeleteStockLocationType();
        $deleteStockLocationType->configureOptions($resolver);
    }
}
