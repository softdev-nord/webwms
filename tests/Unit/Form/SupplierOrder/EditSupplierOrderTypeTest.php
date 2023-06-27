<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\SupplierOrder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Form\SupplierOrder\EditSupplierOrderType;

/**
 * @package:    WebWMS\Tests\Unit\Form\SupplierOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        EditSupplierOrderTypeTest
 *
 * @covers \WebWMS\Form\SupplierOrder\EditSupplierOrderType
 */
final class EditSupplierOrderTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->withConsecutive(
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

        $type = new EditSupplierOrderType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => SupplierOrder::class]);

        $type = new EditSupplierOrderType();
        $type->configureOptions($resolver);
    }
}
