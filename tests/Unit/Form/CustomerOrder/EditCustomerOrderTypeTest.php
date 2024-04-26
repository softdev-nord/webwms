<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\CustomerOrder;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerOrderEntity;
use WebWMS\Form\CustomerOrder\EditCustomerOrderType;

/**
 * @package:    WebWMS\Tests\Unit\Form\CustomerOrderEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        EditCustomerOrderTypeTest
 */
#[CoversClass(EditCustomerOrderType::class)]
final class EditCustomerOrderTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $editCustomerOrderType = new EditCustomerOrderType();
        $editCustomerOrderType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => CustomerOrderEntity::class]);

        $editCustomerOrderType = new EditCustomerOrderType();
        $editCustomerOrderType->configureOptions($resolver);
    }
}
