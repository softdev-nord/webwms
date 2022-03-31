<?php

declare(strict_types=1);

namespace WebWMS\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Order;

/**
 * @package:    WebWMS\Form
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        OrderType
 */
class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('order_id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'inputOrderNr',
                    'id' => 'order_id_1',
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
            ->add('order_nr', TextType::class, [
                'label' => 'Bestellungs-Nr',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'order_nr_1',
                    'data-type' => 'order_nr_1',
                    'style' => 'background-color: transparent',
                    'disabled' => true,
                ],
            ])
            ->add('order_reference', TextType::class, [
                'label' => 'Bestellungs-Referenz',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'aft_ref',
                    'placeholder' => 'Bestellungs-Referenz',
                ],
            ])
            ->add('order_date', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'd.m.Y',
                'label' => 'Bestellungsdatum',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('order_order_date', DateType::class, [
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
