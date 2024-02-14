<?php

declare(strict_types=1);

namespace WebWMS\Form\Stock\StockLayout;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLayoutEntity;

/**
 * @package:    WebWMS\Form\Stock\StockLayoutEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        DeleteStockLayoutType
 */
class DeleteStockLayoutType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    #[Override]
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'stockLocationId',
                    'data-type' => 'stockLocationId',
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

    #[Override]
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => StockLayoutEntity::class,
        ]);
    }
}
