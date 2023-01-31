<?php

declare(strict_types=1);

namespace WebWMS\Form\CustomerOrder;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerOrder;

class EditCustomerOrderType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerOrderId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'inputOrderNr',
                ],
            ])
            ->add('customerId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('customerOrderNr', TextType::class, [
                'label' => 'Auftrags-Nr',
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'disabled' => true,
                ],
            ])
            ->add('customerOrderReference', TextType::class, [
                'label' => 'Auftrags-Referenz',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('customerOrderDate', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'd.m.Y',
                'label' => 'Auftragsdatum',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('customerOrderCreationDate', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'd.m.Y',
                'label' => 'Bestelldatum',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Bestelldatum',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomerOrder::class,
        ]);
    }
}
