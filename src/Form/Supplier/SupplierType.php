<?php

namespace WebWMS\Form\Supplier;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Supplier;

/**
 * @package:    WebWMS\Form\Supplier
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        SupplierType
 */
class SupplierType extends AbstractType
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
                    'style' => 'background-color: transparent',
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
                    'id' => 'supplierAddressCity',
                    'data-type' => 'supplierAddressCity',
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
