<?php

declare(strict_types=1);

namespace WebWMS\Form\Stock\StockLayout;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLayout;

/**
 * @package:    WebWMS\Form\Stock\StockLayout
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        EditStockLayoutType
 */
class EditStockLayoutType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('stockNr', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockNr',
                ],
            ])
            ->add('stockDescription', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockDescription',
                ],
            ])
            ->add('stockLevel1', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockLevel1',
                ],
            ])
            ->add('stockLevel2', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockLevel2',
                ],
            ])
            ->add('stockLevel3', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockLevel3',
                ],
            ])
            ->add('stockLevel4', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockLevel4',
                ],
            ])
            ->add('stockModel', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockModel',
                ],
            ])
            ->add('stockTyp', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockTyp',
                ],
            ])
            ->add('stockLongDescription', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockLongDescription',
                ],
            ])
            ->add('save', ButtonType::class, [
                'label' => 'Änderungen speichern',
                'attr' => [
                    'class' => 'btn btn-lg',
                ],
            ])
            ->add('back_to_stock_layout_overview', ButtonType::class, [
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
            'data_class' => StockLayout::class,
        ]);
    }
}
