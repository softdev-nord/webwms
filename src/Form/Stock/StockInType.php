<?php

namespace WebWMS\Form\Stock;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Article;

/**
 * @package:    WebWMS\Form\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockInType
 */
class StockInType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('article_id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'article_id',
                    'data-type' => 'article_id',
                ],
            ])
            ->add('article_nr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control autocomplete_items ui-autocomplete-input',
                    'id' => 'article_nr',
                    'data-type' => 'article_nr',
                ],
            ])
            ->add('standard_loading_equipment', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'KARTON' => 'KARTON',
                    'PALETTE' => 'PALETTE',
                    'BLOCK' => 'BLOCK'
                ],
            ])
            ->add('le_quantity', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'loading_equipment',
                    'data-type' => 'loading_equipment',
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
                    'id' => 'article_ean',
                    'data-type' => 'article_ean',
                ],
            ])
            ->add('post', SubmitType::class, [
                'label' => 'Buchen',
                'attr' => [
                    'class' => 'btn btn-secondary btn-lg',
                ],
            ])
            ->add('clear_form', ButtonType::class, [
                'label' => 'Formular leeren',
                'attr' => [
                    'class' => 'btn btn-secondary btn-lg',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
