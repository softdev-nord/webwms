<?php

declare(strict_types=1);

namespace WebWMS\Form\Stock;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Form\Stock',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockInType'
)]
class StockInType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('articleId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'articleId',
                    'data-type' => 'articleId',
                ],
            ])
            ->add('articleNr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control autocomplete_items ui-autocomplete-input',
                    'id' => 'articleNr',
                    'data-type' => 'articleNr',
                ],
            ])
            ->add('standardLoadingEquipment', ChoiceType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-select',
                ],
                'choices' => [
                    'KARTON' => 'KARTON',
                    'PALETTE' => 'PALETTE',
                    'BLOCK' => 'BLOCK',
                ],
            ])
            ->add('leQuantity', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'le_quantity',
                    'data-type' => 'le_quantity',
                ],
            ])
            ->add('quantity', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'quantity',
                    'data-type' => 'quantity',
                ],
            ])
            ->add('charge', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'charge',
                    'data-type' => 'charge',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Buchen',
                'attr' => [
                    'class' => 'btn btn-dark bg-gradient waves-effect waves-light',
                ],
            ])
            ->add('abort', ButtonType::class, [
                'label' => 'Abbrechen',
                'attr' => [
                    'class' => 'btn btn-dark bg-gradient waves-effect waves-light',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
