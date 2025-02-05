<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\SupplierOrder;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\SupplierOrderPos;
use WebWMS\Form\SupplierOrder\SupplierOrderPosType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\SupplierOrder',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'SupplierOrderPosTypeTest'
)]
#[CoversClass(SupplierOrderPosType::class)]
final class SupplierOrderPosTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->willReturnOnConsecutiveCalls(
                ['id', HiddenType::class, self::anything()],
                ['supplierOrderId', HiddenType::class, self::anything()],
                ['supplierOrderPosQuantity', TextType::class, self::anything()],
                ['articleId', HiddenType::class, self::anything()],
                ['articleNr', TextType::class, self::anything()],
                ['articleName', TextType::class, self::anything()],
            );

        $supplierOrderPosType = new SupplierOrderPosType();
        $supplierOrderPosType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => SupplierOrderPos::class]);

        $supplierOrderPosType = new SupplierOrderPosType();
        $supplierOrderPosType->configureOptions($resolver);
    }
}
