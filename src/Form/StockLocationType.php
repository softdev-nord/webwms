<?php

namespace WebWMS\Form;

use WebWMS\Entity\StockLocation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @package:    WebWMS\Form
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLocationType
 */
class StockLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stock_location_ln')
            ->add('stock_location_fb')
            ->add('stock_location_sp')
            ->add('stock_location_tf')
            ->add('stock_location_desc')
            ->add('stock_location_width')
            ->add('stock_location_depth')
            ->add('stock_location_height')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StockLocation::class,
        ]);
    }
}
