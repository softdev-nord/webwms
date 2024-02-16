<?php

declare(strict_types=1);

namespace WebWMS\Form\Supplier;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\SupplierEntity;

/**
 * @package:    WebWMS\Form\SupplierEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        AddSupplierType
 */
class AddSupplierType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    #[Override]
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
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
                    'id' => 'supplierAddressCity',
                    'data-type' => 'supplierAddressCity',
                ],
            ])
            ->add('save', ButtonType::class, [
                'label' => 'Lieferanten anlegen',
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
            'data_class' => SupplierEntity::class,
        ]);
    }
}
