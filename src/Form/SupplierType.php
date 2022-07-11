<?php

namespace WebWMS\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Supplier;

/**
 * @package:    WebWMS\Form
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        SupplierType
 */
class SupplierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('supplier_id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplier_id',
                    'data-type' => 'supplier_id',
                ],
            ])
            ->add('supplier_nr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplier_nr',
                    'data-type' => 'supplier_nr',
                    'style' => 'background-color: transparent',
                ],
            ])
            ->add('supplier_name', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplier_name',
                    'data-type' => 'supplier_name',
                ],
            ])
            ->add('supplier_address_addition', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplier_address_addition',
                    'data-type' => 'supplier_address_addition',
                ],
            ])
            ->add('supplier_address_street', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplier_address_street',
                    'data-type' => 'supplier_address_street',
                ],
            ])
            ->add('supplier_address_street_nr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplier_address_street_nr',
                    'data-type' => 'supplier_address_street_nr',
                ],
            ])
            ->add('supplier_address_country_code', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplier_address_country_code',
                    'data-type' => 'supplier_address_country_code',
                ],
            ])
            ->add('supplier_address_zipcode', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplier_address_zipcode',
                    'data-type' => 'supplier_address_zipcode',
                ],
            ])
            ->add('supplier_address_city', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplier_address_city',
                    'data-type' => 'supplier_address_city',
                ],
            ])
            ->add('add_supplier', SubmitType::class, [
                'label' => 'Lieferant anlegen',
                'attr' => [
                    'class' => 'btn btn-secondary btn-lg',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Supplier::class,
        ]);
    }
}
