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
use WebWMS\Form\CustomerOrder\DeleteCustomerOrderType;
use WebWMS\Form\CustomerOrder\EditCustomerOrderType;

/**
 * @package:    WebWMS\Tests\Unit\Form\CustomerOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        DeleteCustomerOrderTypeTest
 *
 * @covers \WebWMS\Form\CustomerOrder\DeleteCustomerOrderType
 */
final class DeleteCustomerOrderTypeTest extends TestCase
{
    private DeleteCustomerOrderType $deleteCustomerOrderType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->deleteCustomerOrderType = new DeleteCustomerOrderType();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->deleteCustomerOrderType);
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
                ['delete', ButtonType::class, [
                    'label' => 'Löschen',
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

        $form = $this->deleteCustomerOrderType;
        $form->buildForm($builder, (array) $optionsResolver);
    }

    public function testConfigureOptions(): void
    {
        $deleteCustomerOrderType = $this->deleteCustomerOrderType;
        $resolver = $this->createMock(OptionsResolver::class);

        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with([
                    'data_class' => CustomerOrder::class,
                ]
            );

        $deleteCustomerOrderType->configureOptions($resolver);
    }
}
