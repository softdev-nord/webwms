<?php

declare(strict_types=1);

namespace WebWMS\Form\Stock;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @package:    WebWMS\Form\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockInFinalType
 */
class StockInFinalType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['data']['freeStockLocations'] as $key => $value) {
            $builder
                ->add('stock_su_id_'.$key, TextType::class, [
                    'label' => false,
                    'attr' => [
                        'id' => 'stock_su_id',
                        'data-type' => 'stock_su_id',
                        'style' => 'text-align: center; background: #3b4245;',
                        'readonly' => true,
                    ],
                ])
                ->add('stock_system', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'id' => 'stock_system',
                        'data-type' => 'stock_system',
                        'style' => 'text-align: center; background: #3b4245;',
                        'readonly' => true,
                    ],
                ])
                ->add('stock_ln', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'id' => 'stock_ln',
                        'data-type' => 'stock_ln',
                        'style' => 'text-align: center; background: #3b4245;',
                        'readonly' => true,
                    ],
                ])
                ->add('stock_fb', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'id' => 'stock_fb',
                        'data-type' => 'stock_fb',
                        'style' => 'text-align: center; background: #3b4245;',
                        'readonly' => true,
                    ],
                ])
                ->add('stock_sp', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'id' => 'stock_sp',
                        'data-type' => 'stock_sp',
                        'style' => 'text-align: center; background: #3b4245;',
                        'readonly' => true,
                    ],
                ])
                ->add('stock_tf', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'id' => 'stock_tf',
                        'data-type' => 'stock_tf',
                        'style' => 'text-align: center; background: #3b4245;',
                        'readonly' => true,
                    ],
                ])
                ->add('stock_quantity', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'id' => 'stock_quantity',
                        'data-type' => 'stock_quantity',
                        'style' => 'text-align: center; background: #3b4245;',
                        'readonly' => true,
                    ],
                ])
                ->add('stock_tbe', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'id' => 'stock_tbe',
                        'data-type' => 'stock_tbe',
                        'style' => 'text-align: center; background: #3b4245;',
                        'readonly' => true,
                    ],
                ]);
        }

        $builder
            ->add('stock_in_post_final', SubmitType::class, [
                'label' => 'Buchen',
                'attr' => [
                    'class' => 'btn btn-secondary btn-lg',
                ],
            ])
            ->add('stock_in_correction', ButtonType::class, [
                'label' => 'Korrektur',
                'attr' => [
                    'class' => 'btn btn-secondary btn-lg',
                ],
            ])
            ->add('stock_in_graphical', ButtonType::class, [
                'label' => 'Grafik',
                'attr' => [
                    'class' => 'btn btn-secondary btn-lg',
                ],
            ])
            ->add('back_to_stock_in', ButtonType::class, [
                'label' => 'Abbruch',
                'attr' => [
                    'class' => 'btn btn-secondary btn-lg',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
            ]);
    }
}
