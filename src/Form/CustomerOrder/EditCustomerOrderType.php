<?php

declare(strict_types=1);

namespace WebWMS\Form\CustomerOrder;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerOrderEntity;

/**
 * @package:    WebWMS\Form\CustomerOrderEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        EditCustomerOrderType
 */
class EditCustomerOrderType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    #[Override]
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('customerOrderId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'customerOrderId',
                ],
            ])
            ->add('customerOrderNr', TextType::class, [
                'label' => 'Auftrags-Nr',
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
                'label' => 'Auftrags-Referenz',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerOrderReference',
                    'placeholder' => 'Auftrags-Referenz',
                ],
            ])
            ->add('customerOrderDate', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'dd.MM.yyyy',
                'label' => 'Auftragsdatum',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('customerOrderCreationDate', DateTimeType::class, [
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
                    'class' => 'btn btn-primary btn3d',
                ],
            ])
            ->add('abort', ButtonType::class, [
                'label' => 'Abbrechen',
                'attr' => [
                    'class' => 'btn btn-primary btn3d abort',
                ],
            ])
        ;
    }

    #[Override]
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => CustomerOrderEntity::class,
        ]);
    }
}
