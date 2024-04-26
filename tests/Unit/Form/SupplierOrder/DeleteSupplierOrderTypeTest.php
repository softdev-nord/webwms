<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\SupplierOrder;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\SupplierOrderEntity;
use WebWMS\Form\SupplierOrder\DeleteSupplierOrderType;

/**
 * @package:    WebWMS\Tests\Unit\Form\SupplierOrderEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        DeleteSupplierOrderTypeTest
 */
#[CoversClass(DeleteSupplierOrderType::class)]
final class DeleteSupplierOrderTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $deleteSupplierOrderType = new DeleteSupplierOrderType();
        $deleteSupplierOrderType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => SupplierOrderEntity::class]);

        $deleteSupplierOrderType = new DeleteSupplierOrderType();
        $deleteSupplierOrderType->configureOptions($resolver);
    }
}
