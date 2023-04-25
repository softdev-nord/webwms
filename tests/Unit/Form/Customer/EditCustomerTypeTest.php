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
use WebWMS\Form\Customer\EditCustomerType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Customer
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        EditCustomerTypeTest
 *
 * @covers \WebWMS\Form\Customer\EditCustomerType
 */
final class EditCustomerTypeTest extends TestCase
{
    private EditCustomerType $editCustomerType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->editCustomerType = new EditCustomerType();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->editCustomerType);
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
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerNr',
                        'data-type' => 'customerNr',
                    ],
                ]],
                ['customerName', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerName',
                        'data-type' => 'customerName',
                    ],
                ]],
                ['customerAddressAddition', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerAddressAddition',
                        'data-type' => 'customerAddressAddition',
                    ],
                ]],
                ['customerAddressStreet', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerAddressStreet',
                        'data-type' => 'customerAddressStreet',
                    ],
                ]],
                ['customerAddressStreetNr', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerAddressStreetNr',
                        'data-type' => 'customerAddressStreetNr',
                    ],
                ]],
                ['customerCountryCode', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerCountryCode',
                        'data-type' => 'customerCountryCode',
                    ],
                ]],
                ['customerZipCode', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerZipCode',
                        'data-type' => 'customerZipCode',
                    ],
                ]],
                ['customerCity', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerCity',
                        'data-type' => 'customerCity',
                    ],
                ]],
                ['save', ButtonType::class, [
                    'label' => 'Änderungen speichern',
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

        $form = $this->editCustomerType;
        $form->buildForm($builder, (array) $optionsResolver);
    }

    public function testConfigureOptions(): void
    {
        $addCustomerType = $this->editCustomerType;
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
