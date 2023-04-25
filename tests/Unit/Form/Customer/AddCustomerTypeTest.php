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
    private AddCustomerType $addCustomerType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->addCustomerType = new AddCustomerType();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->addCustomerType);
    }

    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects(self::exactly(1))
            ->method('add')
            ->withConsecutive(
                ['customerId', HiddenType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerId',
                        'data-type' => 'customerId',
                    ],
                ]],
                ['customerNr', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerNr',
                        'data-type' => 'customerNr',
                    ],
                ]],
                ['customerName', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerName',
                        'data-type' => 'customerName',
                    ],
                ]],
                ['customerAddressAddition', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerAddressAddition',
                        'data-type' => 'customerAddressAddition',
                    ],
                ]],
                ['customerAddressStreet', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerAddressStreet',
                        'data-type' => 'customerAddressStreet',
                    ],
                ]],
                ['customerAddressStreetNr', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerAddressStreetNr',
                        'data-type' => 'customerAddressStreetNr',
                    ],
                ]],
                ['customerCountryCode', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerCountryCode',
                        'data-type' => 'customerCountryCode',
                    ],
                ]],
                ['customerZipCode', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerZipCode',
                        'data-type' => 'customerZipCode',
                    ],
                ]],
                ['customerCity', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerCity',
                        'data-type' => 'customerCity',
                    ],
                ]],
                ['save', ButtonType::class, [
                    'label' => 'Kunden anlegen',
                    'attr' => [
                        'class' => 'btn btn-lg',
                    ],
                ]],
                ['abort', ButtonType::class, [
                    'label' => 'Abbrechen',
                    'attr' => [
                        'class' => 'btn btn-lg abort',
                    ],
                ]],
            );

        $optionsResolver = $this->createMock(OptionsResolver::class);

        $form = $this->addCustomerType;
        $form->buildForm($builder, (array) $optionsResolver);
    }

    public function testConfigureOptions(): void
    {
        $addCustomerType = $this->addCustomerType;
        $resolver = $this->createMock(OptionsResolver::class);

        $resolver->expects(self::once())
            ->method('setDefaults')
            ->with([
                    'data_class' => Customer::class,
                ]
            );

        $addCustomerType->configureOptions($resolver);
    }
}
