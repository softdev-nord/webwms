<?php

declare(strict_types=1);

namespace WebWMS\Form\CustomerOrder;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerOrder;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Form\CustomerOrder',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'AddCustomerOrderType'
)]
class AddCustomerOrderType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerOrderId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'customer_order_id',
                ],
            ])
            ->add('customerOrderNr', TextType::class, [
                'label' => 'customer_order.form.add_customer_order.order_no',
                'attr' => [
                    'class' => 'form-control is--transparent',
                    'id' => 'customer_order_nr',
                ],
            ])
            ->add('usrId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'user_id',
                    'data-type' => 'user_id',
                ],
            ])
            ->add('customerId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'customer_id',
                ],
            ])
            ->add('customerOrderReference', TextType::class, [
                'label' => 'customer_order.form.add_customer_order.order_reference',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customer_order_reference',
                    'placeholder' => 'customer_order.form.add_customer_order.order_reference',
                ],
            ])
            ->add('customerOrderDate', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'dd.MM.yyyy',
                'label' => 'customer_order.form.add_customer_order.order_date',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('customerOrderCreationDate', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'dd.MM.yyyy',
                'label' => 'customer_order.form.add_customer_order.creation_date',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'true',
                ],
            ])
            ->add('save', ButtonType::class, [
                'label' => 'customer_order.form.add_customer_order.create_order',
                'attr' => [
                    'class' => 'btn btn-primary btn3d',
                ],
            ])
            ->add('abort', ButtonType::class, [
                'label' => 'customer_order.form.add_customer_order.cancel',
                'attr' => [
                    'class' => 'btn btn-primary btn3d abort',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomerOrder::class,
        ]);
    }
}
