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

/**
 * @package:    WebWMS\Form\SupplierOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        AddSupplierOrderType
 */
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
                'label' => 'Bestellungs-Nr',
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
                'label' => 'Bestellungs-Referenz',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplier_order_reference',
                    'placeholder' => 'Bestellungs-Referenz',
                ],
            ])
            ->add('supplierOrderDate', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'dd.MM.yyyy',
                'label' => 'Bestelldatum',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('supplierOrderCreationDate', DateTimeType::class, [
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
            ])
            ->add('save', ButtonType::class, [
                'label' => 'Bestellung anlegen',
                'attr' => [
                    'class' => 'btn btn-lg',
                ],
            ])
            ->add('back_to_supplier_order_overview', ButtonType::class, [
                'label' => 'Zurück zur Übersicht',
                'attr' => [
                    'class' => 'btn btn-lg',
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
