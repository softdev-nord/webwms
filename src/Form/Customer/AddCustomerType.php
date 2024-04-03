<?php

declare(strict_types=1);

namespace WebWMS\Form\Customer;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerEntity;

/**
 * @package:    WebWMS\Form
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerType
 */
class AddCustomerType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerId',
                    'data-type' => 'customerId',
                ],
            ])
            ->add('customerNr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerNr',
                    'data-type' => 'customerNr',
                ],
            ])
            ->add('customerName', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerName',
                    'data-type' => 'customerName',
                ],
            ])
            ->add('customerAddressAddition', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerAddressAddition',
                    'data-type' => 'customerAddressAddition',
                ],
            ])
            ->add('customerAddressStreet', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerAddressStreet',
                    'data-type' => 'customerAddressStreet',
                ],
            ])
            ->add('customerAddressStreetNr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerAddressStreetNr',
                    'data-type' => 'customerAddressStreetNr',
                ],
            ])
            ->add('customerCountryCode', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerCountryCode',
                    'data-type' => 'customerCountryCode',
                ],
            ])
            ->add('customerZipCode', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerZipCode',
                    'data-type' => 'customerZipCode',
                ],
            ])
            ->add('customerCity', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerCity',
                    'data-type' => 'customerCity',
                ],
            ])
            ->add('save', ButtonType::class, [
                'label' => 'Kunden anlegen',
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
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomerEntity::class,
        ]);
    }
}
