<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\CustomerOrder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerOrderPos;
use WebWMS\Form\CustomerOrder\CustomerOrderPosType;

/**
 * @package:    WebWMS\Tests\Unit\Form\CustomerOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderPosTypeTest
 *
 * @covers \WebWMS\Form\CustomerOrder\CustomerOrderPosType
 */
final class CustomerOrderPosTypeTest extends TestCase
{
    private CustomerOrderPosType $customerOrderPosType;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->customerOrderPosType = new CustomerOrderPosType();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->customerOrderPosType);
    }

    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects(self::exactly(1))
            ->method('add')
            ->withConsecutive(
                ['id', HiddenType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customer_order_pos_id',
                        'data-type' => 'customer_order_pos_id',
                    ],
                ]],
                ['customerOrderId', HiddenType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customer_order_id',
                        'data-type' => 'customer_order_id',
                    ],
                ]],
                ['quantity', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'data-type' => 'customer_order_pos_quantity',
                    ],
                ]],
                ['articleId', HiddenType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control autocomplete_items',
                        'data-type' => 'article_id',
                    ],
                ]],
                ['articleNr', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control autocomplete_items',
                        'data-type' => 'article_nr',
                    ],
                ]],
                ['articleName', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control autocomplete_items',
                        'data-type' => 'article_name',
                    ],
                ]],
            );

        $optionsResolver = $this->createMock(OptionsResolver::class);

        $form = $this->customerOrderPosType;
        $form->buildForm($builder, (array) $optionsResolver);
    }

    public function testConfigureOptions(): void
    {
        $addCustomerOrderType = $this->customerOrderPosType;
        $resolver = $this->createMock(OptionsResolver::class);

        $resolver->expects(self::once())
            ->method('setDefaults')
            ->with([
                    'data_class' => CustomerOrderPos::class,
                ]
            );

        $addCustomerOrderType->configureOptions($resolver);
    }
}
