<?php

namespace WebWMS\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerOrder;

class CustomerOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer_order_id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'inputAftNr',
                    'id' => 'aft_nr_1',
                    'data-type' => 'aft_id_1',
                ],
            ])
            ->add('customer_id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control autocomplete_customers',
                    'id' => 'id_1',
                    'data-type' => 'id',
                ],
            ])
            ->add('customer_order_nr', TextType::class, [
                'label' => 'Auftrags-Nr',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'aft_nr_1',
                    'data-type' => 'aft_nr_1',
                    'style' => 'background-color: transparent',
                    'disabled' => true,
                ],
            ])
            ->add('customer_order_reference', TextType::class, [
                'label' => 'Auftrags-Referenz',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'aft_ref',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerOrder::class,
        ]);
    }
}
