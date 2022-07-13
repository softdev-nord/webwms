<?php

namespace WebWMS\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLocation;

/**
 * @package:    WebWMS\Form
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        EditStockLocationType
 */
class EditStockLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('id', HiddenType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'id',
                'data-type' => 'id',
            ],
        ]);
        $builder->add('stock_location_ln', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'stock_location_ln',
                'data-type' => 'stock_location_ln',
                'style' => 'background-color: transparent',
            ],
        ]);
        $builder->add('stock_location_fb', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'stock_location_fb',
                'data-type' => 'stock_location_fb',
            ],
        ]);
        $builder->add('stock_location_sp', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'stock_location_sp',
                'data-type' => 'stock_location_sp',
            ],
        ]);
        $builder->add('stock_location_tf', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'stock_location_tf',
                'data-type' => 'stock_location_tf',
            ],
        ]);
        $builder->add('stock_location_coordinate', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'stock_location_coordinate',
                'data-type' => 'stock_location_coordinate',
            ],
        ]);
        $builder->add('stock_location_desc', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'stock_location_desc',
                'data-type' => 'stock_location_desc',
            ],
        ]);
        $builder->add('stock_location_width', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'stock_location_width',
                'data-type' => 'stock_location_width',
            ],
        ]);
        $builder->add('stock_location_depth', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'stock_location_depth',
                'data-type' => 'stock_location_depth',
            ],
        ]);
        $builder->add('stock_location_height', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'stock_location_height',
                'data-type' => 'stock_location_height',
            ],
        ]);
        $builder->add('save', ButtonType::class, [
            'label' => 'Änderungen speichern',
            'attr' => [
                'class' => 'btn btn-secondary btn-lg',
            ],
        ]);
        $builder->add('back_to_stock_location_overview', ButtonType::class, [
            'label' => 'Zurück zur Übersicht',
            'attr' => [
                'class' => 'btn btn-secondary btn-lg',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StockLocation::class,
        ]);
    }
}
