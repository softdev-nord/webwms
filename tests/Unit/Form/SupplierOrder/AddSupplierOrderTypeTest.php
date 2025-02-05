<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\SupplierOrder;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Form\SupplierOrder\AddSupplierOrderType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\SupplierOrder',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'AddSupplierOrderTypeTest'
)]
#[CoversClass(AddSupplierOrderType::class)]
final class AddSupplierOrderTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->willReturnOnConsecutiveCalls(
                ['supplierOrderId', HiddenType::class, self::anything()],
                ['supplierOrderNr', TextType::class, self::anything()],
                ['usrId', HiddenType::class, self::anything()],
                ['supplierId', HiddenType::class, self::anything()],
                ['supplierOrderReference', TextType::class, self::anything()],
                ['supplierOrderDate', TextType::class, self::anything()],
                ['supplierOrderCreationDate', TextType::class, self::anything()],
                ['save', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $addSupplierOrderType = new AddSupplierOrderType();
        $addSupplierOrderType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => SupplierOrder::class]);

        $addSupplierOrderType = new AddSupplierOrderType();
        $addSupplierOrderType->configureOptions($resolver);
    }
}
