<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\SupplierOrder;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\SupplierOrderEntity;
use WebWMS\Form\SupplierOrder\AddSupplierOrderType;

/**
 * @package:    WebWMS\Tests\Unit\Form\SupplierOrderEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        AddSupplierOrderTypeTest
 */
#[CoversClass(AddSupplierOrderType::class)]
final class AddSupplierOrderTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $addSupplierOrderType = new AddSupplierOrderType();
        $addSupplierOrderType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => SupplierOrderEntity::class]);

        $addSupplierOrderType = new AddSupplierOrderType();
        $addSupplierOrderType->configureOptions($resolver);
    }
}
