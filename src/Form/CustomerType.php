<?php

namespace WebWMS\Form;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use WebWMS\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @package:    WebWMS\Form
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerType
 */
class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer_id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customer_id',
                    'data-type' => 'customer_id',
                ],
            ])
            ->add('customer_nr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customer_nr',
                    'data-type' => 'customer_nr',
                    'style' => 'background-color: transparent',
                ],
            ])
            ->add('customer_name', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customer_name',
                    'data-type' => 'customer_name',
                ],
            ])
            ->add('customer_address_addition', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customer_address_addition',
                    'data-type' => 'customer_address_addition',
                ],
            ])
            ->add('customer_address_street', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customer_address_street',
                    'data-type' => 'customer_address_street',
                ],
            ])
            ->add('customer_address_street_nr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customer_address_street_nr',
                    'data-type' => 'customer_address_street_nr',
                ],
            ])
            ->add('customer_country_code', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customer_country_code',
                    'data-type' => 'customer_country_code',
                ],
            ])
            ->add('customer_zip_code', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customer_zip_code',
                    'data-type' => 'customer_zip_code',
                ],
            ])
            ->add('customer_city', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customer_city',
                    'data-type' => 'customer_city',
                ],
            ])
            ->add('add_customer', SubmitType::class, [
                'label' => 'Kunde anlegen',
                'attr' => [
                    'class' => 'btn btn-secondary btn-lg',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
