<?php

declare(strict_types=1);

namespace WebWMS\Form\Stock\StockZone;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockZoneEntity;

/**
 * @package:    WebWMS\Form\Stock\StockZoneEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        AddStockZoneType
 */
class AddStockZoneType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'id',
                    'data-type' => 'id',
                ],
            ])
            ->add('stockZoneShortDesc', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'stockZoneShortDesc',
                    'data-type' => 'stockZoneShortDesc',
                ],
            ])
            ->add('stockZoneDescription', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'stockZoneDescription',
                    'data-type' => 'stockZoneDescription',
                ],
            ])
            ->add('save', ButtonType::class, [
                'label' => 'Lagerzone anlegen',
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StockZoneEntity::class,
        ]);
    }
}
