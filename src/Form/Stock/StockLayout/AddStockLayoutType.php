<?php

declare(strict_types=1);

namespace WebWMS\Form\Stock\StockLayout;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLayoutEntity;

/**
 * @package:    WebWMS\Form\Stock\StockLayoutEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        AddStockLayoutType
 */
class AddStockLayoutType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    #[Override]
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('stockNr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockNr',
                ],
            ])
            ->add('stockDescription', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockDescription',
                ],
            ])
            ->add('stockLevel1', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockLevel1',
                ],
            ])
            ->add('stockLevel2', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockLevel2',
                ],
            ])
            ->add('stockLevel3', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockLevel3',
                ],
            ])
            ->add('stockLevel4', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockLevel4',
                ],
            ])
            ->add('stockModel', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockModel',
                ],
            ])
            ->add('stockTyp', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockTyp',
                ],
            ])
            ->add('stockLongDescription', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockLongDescription',
                ],
            ])
            ->add('save', ButtonType::class, [
                'label' => 'Lagerlayout anlegen',
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
            'data_class' => StockLayoutEntity::class,
        ]);
    }
}
