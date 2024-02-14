<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Customer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerEntity;
use WebWMS\Form\Customer\EditCustomerType;

/**
 * @package:    WebWMS\Tests\Unit\Form\CustomerEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        EditCustomerTypeTest
 */
#[CoversClass(EditCustomerType::class)]
final class EditCustomerTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $editCustomerType = new EditCustomerType();
        $editCustomerType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => CustomerEntity::class]);

        $editCustomerType = new EditCustomerType();
        $editCustomerType->configureOptions($resolverMock);
    }
}
