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
    class: 'EditCustomerOrderType'
)]
class EditCustomerOrderType extends AbstractType
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
              'id' => 'customerOrderId',
            ],
          ])
          ->add('customerOrderNr', TextType::class, [
            'label' => 'form.customerorder.editcustomerordertype.label.0d68b9e1',
            'attr' => [
              'class' => 'form-control is--transparent',
              'id' => 'customerOrderNr',
            ],
          ])
          ->add('usrId', HiddenType::class, [
            'label' => false,
            'attr' => [
              'id' => 'usrId',
              'data-type' => 'usrId',
            ],
          ])
          ->add('customerId', HiddenType::class, [
            'label' => false,
            'attr' => [
              'id' => 'customerId',
            ],
          ])
          ->add('customerOrderReference', TextType::class, [
            'empty_data' => '',
            'label' => 'form.customerorder.editcustomerordertype.label.e89ab1b8',
            'attr' => [
              'class' => 'form-control',
              'id' => 'customerOrderReference',
              'placeholder' => 'form.customerorder.editcustomerordertype.placeholder.e89ab1b8',
            ],
          ])
          ->add('customerOrderDate', DateTimeType::class, [
            'widget' => 'single_text',
            'input' => 'datetime',
            'format' => 'dd.MM.yyyy',
            'label' => 'form.customerorder.editcustomerordertype.label.f5bd3e41',
            'html5' => false,
            'attr' => [
              'class' => 'form-control',
            ],
          ])
          ->add('customerOrderCreationDate', DateTimeType::class, [
            'widget' => 'single_text',
            'input' => 'datetime',
            'format' => 'dd.MM.yyyy',
            'label' => 'form.customerorder.editcustomerordertype.label.286db5c0',
            'html5' => false,
            'attr' => [
              'class' => 'form-control',
              'style' => 'background-color: transparent',
              'readonly' => 'true',
            ],
          ])
          ->add('save', ButtonType::class, [
            'label' => 'form.customerorder.editcustomerordertype.label.f80de8fc',
            'attr' => [
              'class' => 'btn btn-primary btn3d',
            ],
          ])
          ->add('abort', ButtonType::class, [
            'label' => 'form.customerorder.editcustomerordertype.label.2d5d70e9',
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
