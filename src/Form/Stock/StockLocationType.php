<?php

declare(strict_types=1);

namespace WebWMS\Form\Stock;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLocation;
use WebWMS\Entity\StockZone;

/**
 * @package:    WebWMS\Form
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLocationType
 */
class StockLocationType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('stock_location_ln', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stock_location_ln',
                ],
            ])
            ->add('stock_location_fb', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stock_location_fb',
                ],
            ])
            ->add('stock_location_sp', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stock_location_sp',
                ],
            ])
            ->add('stock_location_tf', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stock_location_tf',
                ],
            ])
            ->add('stock_location_desc', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stock_location_desc',
                ],
            ])
            ->add('stock_location_width', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stock_location_width',
                ],
            ])
            ->add('stock_location_depth', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stock_location_depth',
                ],
            ])
            ->add('stock_location_height', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stock_location_height',
                ],
            ])
            ->add('stock_location_zone', EntityType::class, [
                'label' => false,
                'class' => StockZone::class,
                'choice_label' => 'zone_short_desc',
            ])
            /*->add('stock_location_zone', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'stock_location_zone',
                ],
            ])*/
            ->add('stock_location_check', CheckboxType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
            ])
            ->add('add_stock_location', SubmitType::class, [
                'label' => 'Lagerplatz anlegen',
                'attr' => [
                    'class' => 'btn btn-secondary btn-lg',
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
