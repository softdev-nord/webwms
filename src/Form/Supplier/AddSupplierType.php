<?php

namespace WebWMS\Form\Supplier;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Supplier;

/**
 * @package:    WebWMS\Form\Supplier
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        AddSupplierType
 */
class AddSupplierType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('supplierId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplierId',
                    'data-type' => 'supplierId',
                ],
            ])
            ->add('supplierNr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplierNr',
                    'data-type' => 'supplierNr',
                ],
            ])
            ->add('supplierName', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplierName',
                    'data-type' => 'supplierName',
                ],
            ])
            ->add('supplierAddressAddition', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplierAddressAddition',
                    'data-type' => 'supplierAddressAddition',
                ],
            ])
            ->add('supplierAddressStreet', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplierAddressStreet',
                    'data-type' => 'supplierAddressStreet',
                ],
            ])
            ->add('supplierAddressStreetNr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplierAddressStreetNr',
                    'data-type' => 'supplierAddressStreetNr',
                ],
            ])
            ->add('supplierAddressCountryCode', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplierAddressCountryCode',
                    'data-type' => 'supplierAddressCountryCode',
                ],
            ])
            ->add('supplierAddressZipcode', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplierAddressZipcode',
                    'data-type' => 'supplierAddressZipcode',
                ],
            ])
            ->add('supplierAddressCity', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customerCity',
                    'data-type' => 'customerCity',
                ],
            ])
            ->add('save', ButtonType::class, [
                'label' => 'Lieferanten anlegen',
                'attr' => [
                    'class' => 'btn btn-lg',
                ],
            ])
            ->add('back_to_supplier_overview', ButtonType::class, [
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
            'data_class' => Supplier::class,
        ]);
    }
}
