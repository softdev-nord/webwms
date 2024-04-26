<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Supplier;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\SupplierEntity;
use WebWMS\Form\Supplier\EditSupplierType;

/**
 * @package:    WebWMS\Tests\Unit\Form\SupplierEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        EditSupplierTypeTest
 */
#[CoversClass(EditSupplierType::class)]
final class EditSupplierTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $editSupplierType = new EditSupplierType();
        $editSupplierType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => SupplierEntity::class]);

        $editSupplierType = new EditSupplierType();
        $editSupplierType->configureOptions($resolverMock);
    }
}
