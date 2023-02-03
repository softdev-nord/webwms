<?php

declare(strict_types=1);

namespace WebWMS\Form\SupplierOrder;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\SupplierOrder;

/**
 * @package:    WebWMS\Form
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        SupplierOrderType
 */
class SupplierOrderType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('supplier_order_id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'inputOrderNr',
                    'id' => 'supplier_order_id_1',
                ],
            ])
            ->add('supplier_id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control autocomplete_suppliers',
                    'id' => 'id_1',
                    'data-type' => 'id',
                ],
            ])
            ->add('supplier_order_nr', TextType::class, [
                'label' => 'Bestellungs-Nr',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplier_order_nr_1',
                    'data-type' => 'supplier_order_nr_1',
                    'style' => 'background-color: transparent',
                    'disabled' => true,
                ],
            ])
            ->add('supplier_order_reference', TextType::class, [
                'label' => 'Bestellungs-Referenz',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'aft_ref',
                    'placeholder' => 'Bestellungs-Referenz',
                ],
            ])
            ->add('supplier_order_date', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'd.m.Y',
                'label' => 'Bestellungsdatum',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('supplier_order_creation_date', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'd.m.Y',
                'label' => 'Bestelldatum',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Bestelldatum',
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
