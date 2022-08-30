<?php

namespace WebWMS\Form\Stock;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @package:    WebWMS\Form\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockInFinalCollectionType
 */
class StockInFinalCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('freeStockLocations', CollectionType::class, [
            'entry_type' => StockInFinalType::class,
            'entry_options'  => [
                'attr' => [
                    'class' => 'image-box'
                ]
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null
        ]);
    }
}
