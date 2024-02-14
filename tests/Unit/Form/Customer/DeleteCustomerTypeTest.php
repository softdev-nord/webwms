<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Customer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerEntity;
use WebWMS\Form\Customer\DeleteCustomerType;

/**
 * @package:    WebWMS\Tests\Unit\Form\CustomerEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        DeleteCustomerTypeTest
 */
#[CoversClass(DeleteCustomerType::class)]
final class DeleteCustomerTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $deleteCustomerType = new DeleteCustomerType();
        $deleteCustomerType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => CustomerEntity::class]);

        $deleteCustomerType = new DeleteCustomerType();
        $deleteCustomerType->configureOptions($resolver);
    }
}
