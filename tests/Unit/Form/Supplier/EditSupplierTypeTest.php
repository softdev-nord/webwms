<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Supplier;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Supplier;
use WebWMS\Form\Supplier\EditSupplierType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\Supplier',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'EditSupplierTypeTest'
)]
#[CoversClass(EditSupplierType::class)]
final class EditSupplierTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->willReturnOnConsecutiveCalls(
                ['supplierId', HiddenType::class, self::anything()],
                ['supplierNr', TextType::class, self::anything()],
                ['supplierName', TextType::class, self::anything()],
                ['supplierAddressAddition', TextType::class, self::anything()],
                ['supplierAddressStreet', TextType::class, self::anything()],
                ['supplierAddressStreetNr', TextType::class, self::anything()],
                ['supplierAddressCountryCode', TextType::class, self::anything()],
                ['supplierAddressZipcode', TextType::class, self::anything()],
                ['supplierAddressCity', TextType::class, self::anything()],
                ['save', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $editSupplierType = new EditSupplierType();
        $editSupplierType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => Supplier::class]);

        $editSupplierType = new EditSupplierType();
        $editSupplierType->configureOptions($resolverMock);
    }
}
