<?php

declare(strict_types=1);

namespace WebWMS\Form\SupplierOrder;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Form\SupplierOrder',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'AddSupplierOrderType'
)]
class AddSupplierOrderType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
          ->add('supplierOrderId', HiddenType::class, [
            'label' => false,
            'attr' => [
              'id' => 'supplier_order_id',
            ],
          ])
          ->add('supplierOrderNr', TextType::class, [
            'label' => 'supplier_order.form.add_supplier_order.purchase_order_no',
            'attr' => [
              'class' => 'form-control is--transparent',
              'id' => 'supplier_order_nr',
            ],
          ])
          ->add('usrId', HiddenType::class, [
            'label' => false,
            'attr' => [
              'id' => 'user_id',
              'data-type' => 'user_id',
            ],
          ])
          ->add('supplierId', HiddenType::class, [
            'label' => false,
            'attr' => [
              'id' => 'supplier_id',
            ],
          ])
          ->add('supplierOrderReference', TextType::class, [
            'label' => 'supplier_order.form.add_supplier_order.purchase_order_reference',
            'attr' => [
              'class' => 'form-control',
              'id' => 'supplier_order_reference',
              'placeholder' => 'supplier_order.form.add_supplier_order.purchase_order_reference',
            ],
          ])
          ->add('supplierOrderDate', DateTimeType::class, [
            'widget' => 'single_text',
            'input' => 'datetime',
            'format' => 'dd.MM.yyyy',
            'label' => 'supplier_order.form.add_supplier_order.order_date',
            'html5' => false,
            'attr' => [
              'class' => 'form-control',
            ],
          ])
          ->add('supplierOrderCreationDate', DateTimeType::class, [
            'widget' => 'single_text',
            'input' => 'datetime',
            'format' => 'dd.MM.yyyy',
            'label' => 'supplier_order.form.add_supplier_order.creation_date',
            'html5' => false,
            'attr' => [
              'class' => 'form-control',
              'style' => 'background-color: transparent',
              'readonly' => 'true',
            ],
          ])
          ->add('save', ButtonType::class, [
            'label' => 'supplier_order.form.add_supplier_order.create_purchase_order',
            'attr' => [
              'class' => 'btn btn-primary btn3d',
            ],
          ])
          ->add('abort', ButtonType::class, [
            'label' => 'supplier_order.form.add_supplier_order.cancel',
            'attr' => [
              'class' => 'btn btn-primary btn3d abort',
            ],
          ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
          'data_class' => SupplierOrder::class,
        ]);
    }
}
