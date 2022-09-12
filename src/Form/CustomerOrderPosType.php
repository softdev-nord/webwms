<?php

declare(strict_types=1);

namespace WebWMS\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\CustomerOrderPos;

/**
 * @package:    WebWMS\Form
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        CustomerOrderPosType
 */
class CustomerOrderPosType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer_order_id', HiddenType::class, [
                'data_class' => null,
                'label' => false,
                'attr' => [
                    'class' => 'form-control autocomplete_items',
                    'id' => 'customer_order_id',
                ],
            ])
            ->add('article_id', HiddenType::class, [
                'data_class' => null,
                'label' => false,
                'attr' => [
                    'class' => 'form-control autocomplete_items',
                    'id' => 'id_1',
                ],
            ])
            ->add('customer_order_pos_quantity', TextType::class, [
                'data_class' => null,
                'label' => false,
                'attr' => [
                    'class' => 'form-control autocomplete_items',
                    'id' => 'customer_order_pos_quantity',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerOrderPos::class,
        ]);
    }
}
