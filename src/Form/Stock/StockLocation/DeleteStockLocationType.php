<?php

declare(strict_types=1);

namespace WebWMS\Form\Stock\StockLocation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLocation;

/**
 * @package:    WebWMS\Form\Stock\StockLocation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        DeleteStockLocationType
 */
class DeleteStockLocationType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('stockLocationCoordinate', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'stockLocationCoordinate',
                    'data-type' => 'stockLocationCoordinate',
                ],
            ])
            ->add('delete', ButtonType::class, [
                'label' => 'Löschen',
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
            'data_class' => StockLocation::class,
        ]);
    }
}
