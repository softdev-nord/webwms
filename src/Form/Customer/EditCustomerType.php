<?php

declare(strict_types=1);

namespace WebWMS\Form\Customer;

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
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        EditCustomerType
 */
class EditCustomerType extends AbstractType
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker
    ) {
    }

    /**
     * @SuppressWarnings("unused")
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
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
            ]);
        if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $builder
                ->add('customerNr', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerNr',
                        'data-type' => 'customerNr',
                        'style' => 'background-color: transparent',
                        'readonly' => 'readonly',
                    ],
                ]);
        } else {
            $builder
                ->add('customerNr', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'customerNr',
                        'data-type' => 'customerNr',
                        'style' => 'background-color: transparent',
                    ],
                ]);
        }
        $builder
            ->add('customerName', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerName',
                    'data-type' => 'customerName',
                ],
            ])
            ->add('customerAddressAddition', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerAddressAddition',
                    'data-type' => 'customerAddressAddition',
                ],
            ])
            ->add('customerAddressStreet', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerAddressStreet',
                    'data-type' => 'customerAddressStreet',
                ],
            ])
            ->add('customerAddressStreetNr', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerAddressStreetNr',
                    'data-type' => 'customerAddressStreetNr',
                ],
            ])
            ->add('customerCountryCode', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerCountryCode',
                    'data-type' => 'customerCountryCode',
                ],
            ])
            ->add('customerZipCode', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerZipCode',
                    'data-type' => 'customerZipCode',
                ],
            ])
            ->add('customerCity', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerCity',
                    'data-type' => 'customerCity',
                ],
            ])
            ->add('save', ButtonType::class, [
                'label' => 'Änderungen speichern',
                'attr' => [
                    'class' => 'btn btn-lg',
                ],
            ])
            ->add('abort', ButtonType::class, [
                'label' => 'Abbrechen',
                'attr' => [
                    'class' => 'btn btn-lg abort',
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
