<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Form\Stock\StockInFinalCollectionType;
use WebWMS\Form\Stock\StockInFinalType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\Stock',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'StockInFinalCollectionTypeTest'
)]
#[CoversClass(StockInFinalCollectionType::class)]
final class StockInFinalCollectionTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects($this->once())
            ->method('add')
            ->with('freeStockLocations', CollectionType::class, self::callback(fn (array $options): bool => $options['entry_type'] === StockInFinalType::class));

        $stockInFinalCollectionType = new StockInFinalCollectionType();
        $stockInFinalCollectionType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with([
                'data_class' => StockInFinalType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ]);

        $stockInFinalCollectionType = new StockInFinalCollectionType();
        $stockInFinalCollectionType->configureOptions($resolver);
    }
}
