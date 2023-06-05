<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Supplier;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Supplier;
use WebWMS\Form\Supplier\DeleteSupplierType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Supplier
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        DeleteSupplierTypeTest
 *
 * @covers \WebWMS\Form\Supplier\DeleteSupplierType
 */
final class DeleteSupplierTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->withConsecutive(
                ['supplierId', HiddenType::class, self::anything()],
                ['save', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $type = new DeleteSupplierType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => Supplier::class]);

        $type = new DeleteSupplierType();
        $type->configureOptions($resolverMock);
    }
}
