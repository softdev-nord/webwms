<?php

declare(strict_types=1);

namespace WebWMS\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerOrder;

/**
 * @package:    WebWMS\Form
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerOrderType
 */
class CustomerOrderType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer_order_id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'inputAftNr',
                    'id' => 'customer_order_id',
                    'data-type' => 'customer_order_id',
                ],
            ])
            ->add('customer_id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control autocomplete_customers',
                    'id' => 'customer_id',
                    'data-type' => 'customer_id',
                ],
            ])
            ->add('usr_id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control autocomplete_customers',
                    'id' => 'usr_id',
                    'data-type' => 'usr_id',
                ],
            ])
            ->add('customer_order_nr', TextType::class, [
                'label' => 'Auftrags-Nr',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customer_order_nr',
                    'data-type' => 'customer_order_nr',
                    'style' => 'background-color: transparent',
                    'readonly' => true,
                ],
            ])
            ->add('customer_order_reference', TextType::class, [
                'label' => 'Auftrags-Referenz',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customer_order_reference',
                    'placeholder' => 'Auftrags-Referenz',
                ],
            ])
            ->add('customer_order_date', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'd.m.Y',
                'label' => 'Auftragsdatum',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('customer_order_order_date', DateType::class, [
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
            ->add('add_customer_order', SubmitType::class, [
                'label' => 'Auftrag anlegen',
                'attr' => [
                    'class' => 'btn btn-secondary btn-lg',
                ],
            ])
        ;

        $builder
            ->add('details', CollectionType::class, [
                'entry_type' => CustomerOrderPosType::class,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerOrder::class,
        ]);
    }
}
