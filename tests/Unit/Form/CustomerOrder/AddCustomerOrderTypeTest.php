<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\CustomerOrder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerOrder;
use WebWMS\Form\CustomerOrder\AddCustomerOrderType;

/**
 * @package:    WebWMS\Tests\Unit\Form\CustomerOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        AddCustomerOrderTypeTest
 *
 * @covers \WebWMS\Form\CustomerOrder\AddCustomerOrderType
 */
final class AddCustomerOrderTypeTest extends TestCase
{
    private AddCustomerOrderType $addCustomerOrderType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->addCustomerOrderType = new AddCustomerOrderType();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->addCustomerOrderType);
    }

    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects($this->exactly(1))
            ->method('add')
            ->withConsecutive(
                ['customerOrderId', HiddenType::class, [
                    'label' => false,
                    'attr' => [
                        'id' => 'customer_order_id',
                    ],
                ]],
                ['customerOrderNr', TextType::class, [
                    'label' => 'Auftrags-Nr',
                    'attr' => [
                        'class' => 'form-control is--transparent',
                        'id' => 'customer_order_nr',
                    ],
                ]],
                ['usrId', HiddenType::class, [
                    'label' => false,
                    'attr' => [
                        'id' => 'user_id',
                        'data-type' => 'user_id',
                    ],
                ]],
                ['customerId', HiddenType::class, [
                    'label' => false,
                    'attr' => [
                        'id' => 'customer_id',
                    ],
                ]],
                ['customerOrderReference', TextType::class, [
                    'label' => 'Auftrags-Referenz',
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customer_order_reference',
                        'placeholder' => 'Auftrags-Referenz',
                    ],
                ]],
                ['customerOrderDate', DateTimeType::class, [
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'format' => 'dd.MM.yyyy',
                    'label' => 'Auftragsdatum',
                    'html5' => false,
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]],
                ['customerOrderCreationDate', DateTimeType::class, [
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'format' => 'dd.MM.yyyy',
                    'label' => 'Anlagedatum',
                    'html5' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'style' => 'background-color: transparent',
                        'readonly' => 'true',
                    ],
                ]],
                ['save', ButtonType::class, [
                    'label' => 'Auftrag anlegen',
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

        $form = $this->addCustomerOrderType;
        $form->buildForm($builder, (array) $optionsResolver);
    }

    public function testConfigureOptions(): void
    {
        $addCustomerOrderType = $this->addCustomerOrderType;
        $resolver = $this->createMock(OptionsResolver::class);

        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with([
                    'data_class' => CustomerOrder::class,
                ]
            );

        $addCustomerOrderType->configureOptions($resolver);
    }
}
