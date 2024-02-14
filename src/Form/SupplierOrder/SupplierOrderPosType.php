<?php

declare(strict_types=1);

namespace WebWMS\Form\SupplierOrder;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\SupplierOrderPosEntity;

/**
 * @package:    WebWMS\Form\SupplierOrderEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderPosType
 */
class SupplierOrderPosType extends AbstractType
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
                    'id' => 'supplier_order_pos_id',
                    'data-type' => 'supplier_order_pos_id',
                ],
            ])
            ->add('supplierOrderId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'supplier_order_id',
                    'data-type' => 'supplier_order_id',
                ],
            ])
            ->add('supplierOrderPosQuantity', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-type' => 'supplier_order_pos_quantity',
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
            'data_class' => SupplierOrderPosEntity::class,
        ]);
    }
}
