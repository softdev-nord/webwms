<?php

namespace WebWMS\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use WebWMS\Entity\Customer;

/**
 * @package:    WebWMS\Form
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        EditCustomerType
 */
class EditCustomerType extends AbstractType
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('customer_id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customer_id',
                    'data-type' => 'customer_id',
                ],
            ]);
        if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $builder
                ->add('customer_nr', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customer_nr',
                        'data-type' => 'customer_nr',
                        'style' => 'background-color: transparent',
                        'readonly' => 'readonly',
                    ],
                ]);
        } else {
            $builder->add('customer_nr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customer_nr',
                    'data-type' => 'customer_nr',
                    'style' => 'background-color: transparent',
                ],
            ]);
        }
        $builder->add('customer_name', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'customer_name',
                'data-type' => 'customer_name',
            ],
        ]);
        $builder->add('customer_address_addition', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customer_address_addition',
                    'data-type' => 'customer_address_addition',
                ],
        ]);
        $builder->add('customer_address_street', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'customer_address_street',
                'data-type' => 'customer_address_street',
            ],
        ]);
        $builder->add('customer_address_street_nr', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'customer_address_street_nr',
                'data-type' => 'customer_address_street_nr',
            ],
        ]);
        $builder->add('customer_country_code', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'customer_country_code',
                'data-type' => 'customer_country_code',
            ],
        ]);
        $builder->add('customer_zip_code', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'customer_zip_code',
                'data-type' => 'customer_zip_code',
            ],
        ]);
        $builder->add('customer_city', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'customer_city',
                'data-type' => 'customer_city',
            ],
        ]);
        $builder->add('save', ButtonType::class, [
            'label' => 'Änderungen speichern',
            'attr' => [
                'class' => 'btn btn-secondary btn-lg',
            ],
        ]);
        $builder->add('back_to_customer_overview', ButtonType::class, [
            'label' => 'Zurück zur Übersicht',
            'attr' => [
                'class' => 'btn btn-secondary btn-lg',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
