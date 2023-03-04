<?php

declare(strict_types=1);

namespace WebWMS\Form\CustomerOrder;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerOrder;

/**
 * @package:    WebWMS\Form\CustomerOrder
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        EditCustomerOrderType
 */
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
                    'class' => 'inputAftNr',
                    'id' => 'customerOrderId',
                    'data-type' => 'customerOrderId',
                ],
            ])
            ->add('customerId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control autocomplete_customers',
                    'id' => 'customerId',
                    'data-type' => 'customerId',
                ],
            ])
            ->add('usrId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control autocomplete_customers',
                    'id' => 'usrId',
                    'data-type' => 'usrId',
                ],
            ])
            ->add('customerOrderNr', TextType::class, [
                'empty_data' => '',
                'label' => 'Auftrags-Nr',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerOrderNr',
                    'data-type' => 'customerOrderNr',
                    'style' => 'background-color: transparent',
                    'readonly' => true,
                ],
            ])
            ->add('customerOrderReference', TextType::class, [
                'empty_data' => '',
                'label' => 'Auftrags-Referenz',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerOrderReference',
                    'placeholder' => 'Auftrags-Referenz',
                ],
            ])
            ->add('customerOrderDate', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'dd.MM.yyyy',
                'label' => 'Auftragsdatum',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('customerOrderCreationDate', DateType::class, [
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
                'label' => 'Änderungen speichern',
                'attr' => [
                    'class' => 'btn btn-lg',
                ],
            ])
            ->add('back_to_customer_order_overview', ButtonType::class, [
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
            'data_class' => CustomerOrder::class,
        ]);
    }
}
