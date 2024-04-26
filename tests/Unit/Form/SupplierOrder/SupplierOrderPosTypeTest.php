<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\SupplierOrder;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\SupplierOrderPosEntity;
use WebWMS\Form\SupplierOrder\SupplierOrderPosType;

/**
 * @package:    WebWMS\Tests\Unit\Form\SupplierOrderEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        SupplierOrderPosTypeTest
 */
#[CoversClass(SupplierOrderPosType::class)]
final class SupplierOrderPosTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $supplierOrderPosType = new SupplierOrderPosType();
        $supplierOrderPosType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => SupplierOrderPosEntity::class]);

        $supplierOrderPosType = new SupplierOrderPosType();
        $supplierOrderPosType->configureOptions($resolver);
    }
}
