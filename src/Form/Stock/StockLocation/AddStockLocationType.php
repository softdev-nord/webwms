<?php

declare(strict_types=1);

namespace WebWMS\Form\Stock\StockLocation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLocation;

/**
 * @package:    WebWMS\Form\Stock\StockLocation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        AddStockLocationType
 */
class AddStockLocationType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('stockLocationLn', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockLocationLn',
                ],
            ])
            ->add('stockLocationFb', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockLocationFb',
                ],
            ])
            ->add('stockLocationSp', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockLocationSp',
                ],
            ])
            ->add('stockLocationTf', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockLocationTf',
                ],
            ])
            ->add('stockLocationDesc', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockLocationDesc',
                ],
            ])
            ->add('stockLocationWidth', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockLocationWidth',
                ],
            ])
            ->add('stockLocationDepth', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stockLocationDepth',
                ],
            ])
            ->add('stockLocationHeight', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stock_location_height',
                ],
            ])
            ->add('stockLocationZone', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'ANBRUCH' => 'ANBRUCH',
                    'BLOCK' => 'BLOCK',
                    'DL REGAL' => 'DL REGAL',
                    'KOMMI' => 'KOMMI',
                    'KOMPAL100' => 'KOMPAL100',
                    'KOMPAL200' => 'KOMPAL200',
                    'NASPAL100' => 'NASPAL100',
                    'NASPAL200' => 'NASPAL200',
                    'WA' => 'WA',
                ],
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('stock_location_check', CheckboxType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Lagerplatz anlegen',
                'attr' => [
                    'class' => 'btn btn-lg',
                ],
            ])
            ->add('abort', ButtonType::class, [
                'label' => 'Abbrechen',
                'attr' => [
                    'class' => 'btn btn-lg abort',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StockLocation::class,
        ]);
    }
}
