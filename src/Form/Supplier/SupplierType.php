<?php

declare(strict_types=1);

namespace WebWMS\Form\Supplier;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        // dd($options);
        $isCreateSupplier = $options['isCreateSupplier'];

        $builder
            ->add('supplierId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'supplierId',
                    'data-type' => 'supplierId',
                ],
            ])
            ->add('supplierNr', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Über Lieferanten-Nr suchen',
                    'class' => 'form-control autocomplete_suppliers',
                    'id' => 'supplier_nr',
                    'data-type' => 'supplier_nr',
                    'style' => 'background-color: transparent',
                ],
            ])
            ->add('supplierName', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Lieferanten Name',
                    'class' => 'form-control autocomplete_suppliers',
                    'id' => 'supplier_name',
                    'data-type' => 'supplier_name',
                ],
            ])
            ->add('supplierAddressAddition', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Anschrift Zusatz',
                    'class' => 'form-control autocomplete_suppliers',
                    'id' => 'supplier_address_addition',
                    'data-type' => 'supplier_address_addition',
                ],
            ])
            ->add('supplierAddressStreet', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Straße',
                    'class' => 'form-control autocomplete_suppliers',
                    'id' => 'supplier_address_street',
                    'data-type' => 'supplier_address_street',
                ],
            ])
            ->add('supplierAddressStreetNr', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Haus-Nr.',
                    'class' => 'form-control autocomplete_suppliers',
                    'id' => 'supplier_address_street_nr',
                    'data-type' => 'supplier_address_street_nr',
                ],
            ])
            ->add('supplierAddressCountryCode', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Land',
                    'class' => 'form-control autocomplete_suppliers',
                    'id' => 'supplier_country_code',
                    'data-type' => 'supplier_country_code',
                ],
            ])
            ->add('supplierAddressZipcode', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'PLZ',
                    'class' => 'form-control autocomplete_suppliers',
                    'id' => 'supplier_zip_code',
                    'data-type' => 'supplier_zip_code',
                ],
            ])
            ->add('supplierAddressCity', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Ort',
                    'class' => 'form-control autocomplete_suppliers',
                    'id' => 'supplier_city',
                    'data-type' => 'supplier_city',
                ],
            ]);
        if ($isCreateSupplier) {
            $builder->add('add_supplier', SubmitType::class, [
                'label' => 'Lieferant anlegen',
                'attr' => [
                    'class' => 'btn btn-secondary btn-lg',
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'isCreateSupplier' => [],
        ]);
    }
}
