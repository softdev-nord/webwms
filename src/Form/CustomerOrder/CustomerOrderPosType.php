<?php

declare(strict_types=1);

namespace WebWMS\Form\CustomerOrder;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerOrderPosEntity;

/**
 * @package:    WebWMS\Form\CustomerOrderEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderPosType
 */
class CustomerOrderPosType extends AbstractType
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
                    'id' => 'customer_order_pos_id',
                    'data-type' => 'customer_order_pos_id',
                ],
            ])
            ->add('customerOrderId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'customer_order_id',
                    'data-type' => 'customer_order_id',
                ],
            ])
            ->add('quantity', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'customer_order_pos_quantity',
                ],
            ])
            ->add('articleId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control autocomplete_items',
                    'data-type' => 'article_id',
                ],
            ])
            ->add('articleNr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control autocomplete_items',
                    'data-type' => 'article_nr',
                ],
            ])
            ->add('articleName', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control autocomplete_items',
                    'data-type' => 'article_name',
                ],
            ])
        ;
    }

    #[Override]
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => CustomerOrderPosEntity::class,
        ]);
    }
}
