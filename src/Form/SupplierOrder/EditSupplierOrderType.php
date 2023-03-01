<?php

declare(strict_types=1);

namespace WebWMS\Form\SupplierOrder;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\SupplierOrder;

class EditSupplierOrderType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('supplierOrderId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'inputOrderNr',
                ],
            ])
            ->add('supplierId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('supplierOrderNr', TextType::class, [
                'label' => 'Bestellungs-Nr',
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'disabled' => true,
                ],
            ])
            ->add('supplierOrderReference', TextType::class, [
                'label' => 'Bestellungs-Referenz',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('supplierOrderDate', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'd.m.Y',
                'label' => 'Bestellungsdatum',
                'html5' => false,
            ])
            ->add('supplierOrderCreationDate', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'd.m.Y',
                'label' => 'Bestelldatum',
                'html5' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupplierOrder::class,
        ]);
    }
}
