<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Customer;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Customer;
use WebWMS\Form\Customer\AddCustomerType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Customer
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        AddCustomerTypeTest
 *
 * @covers \WebWMS\Form\Customer\AddCustomerType
 */
final class AddCustomerTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->withConsecutive(
                ['customerId', HiddenType::class, self::anything()],
                ['customerNr', TextType::class, self::anything()],
                ['customerName', TextType::class, self::anything()],
                ['customerAddressAddition', TextType::class, self::anything()],
                ['customerAddressStreet', TextType::class, self::anything()],
                ['customerAddressStreetNr', TextType::class, self::anything()],
                ['customerCountryCode', TextType::class, self::anything()],
                ['customerZipCode', TextType::class, self::anything()],
                ['customerCity', TextType::class, self::anything()],
                ['save', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $type = new AddCustomerType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => Customer::class]);

        $type = new AddCustomerType();
        $type->configureOptions($resolverMock);
    }
}
