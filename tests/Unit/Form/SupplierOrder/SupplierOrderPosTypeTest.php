<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\SupplierOrder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\SupplierOrderPos;
use WebWMS\Form\SupplierOrder\SupplierOrderPosType;

/**
 * @package:    WebWMS\Tests\Unit\Form\SupplierOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderPosTypeTest
 *
 * @covers \WebWMS\Form\SupplierOrder\SupplierOrderPosType
 */
final class SupplierOrderPosTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->withConsecutive(
                ['id', HiddenType::class, self::anything()],
                ['supplierOrderId', HiddenType::class, self::anything()],
                ['supplierOrderPosQuantity', TextType::class, self::anything()],
                ['articleId', HiddenType::class, self::anything()],
                ['articleNr', TextType::class, self::anything()],
                ['articleName', TextType::class, self::anything()],
            );

        $type = new SupplierOrderPosType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => SupplierOrderPos::class]);

        $type = new SupplierOrderPosType();
        $type->configureOptions($resolver);
    }
}
