<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\SupplierOrder;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\SupplierOrderEntity;
use WebWMS\Form\SupplierOrder\EditSupplierOrderType;

/**
 * @package:    WebWMS\Tests\Unit\Form\SupplierOrderEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        EditSupplierOrderTypeTest
 */
#[CoversClass(EditSupplierOrderType::class)]
final class EditSupplierOrderTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $editSupplierOrderType = new EditSupplierOrderType();
        $editSupplierOrderType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => SupplierOrderEntity::class]);

        $editSupplierOrderType = new EditSupplierOrderType();
        $editSupplierOrderType->configureOptions($resolver);
    }
}
