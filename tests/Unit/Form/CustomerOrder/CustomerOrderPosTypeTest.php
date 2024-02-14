<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\CustomerOrder;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerOrderPosEntity;
use WebWMS\Form\CustomerOrder\CustomerOrderPosType;

/**
 * @package:    WebWMS\Tests\Unit\Form\CustomerOrderEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderPosTypeTest
 */
#[CoversClass(CustomerOrderPosType::class)]
final class CustomerOrderPosTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $customerOrderPosType = new CustomerOrderPosType();
        $customerOrderPosType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => CustomerOrderPosEntity::class]);

        $customerOrderPosType = new CustomerOrderPosType();
        $customerOrderPosType->configureOptions($resolver);
    }
}
