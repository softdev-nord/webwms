<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Supplier;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\SupplierEntity;
use WebWMS\Form\Supplier\AddSupplierType;

/**
 * @package:    WebWMS\Tests\Unit\Form\SupplierEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        AddSupplierTypeTest
 */
#[CoversClass(AddSupplierType::class)]
final class AddSupplierTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $addSupplierType = new AddSupplierType();
        $addSupplierType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => SupplierEntity::class]);

        $addSupplierType = new AddSupplierType();
        $addSupplierType->configureOptions($resolverMock);
    }
}
