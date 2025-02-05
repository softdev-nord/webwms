<?php

declare(strict_types=1);

namespace WebWMS\Form\Stock\StockLayout;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLayout;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Form\Stock\StockLayout',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'AddStockLayoutType'
)]
class AddStockLayoutType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
          ->add('stockNr', TextType::class, [
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stockNr',
            ],
          ])
          ->add('stockDescription', TextType::class, [
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stockDescription',
            ],
          ])
          ->add('stockLevel1', TextType::class, [
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stockLevel1',
            ],
          ])
          ->add('stockLevel2', TextType::class, [
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stockLevel2',
            ],
          ])
          ->add('stockLevel3', TextType::class, [
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stockLevel3',
            ],
          ])
          ->add('stockLevel4', TextType::class, [
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stockLevel4',
            ],
          ])
          ->add('stockModel', TextType::class, [
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stockModel',
            ],
          ])
          ->add('stockTyp', TextType::class, [
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stockTyp',
            ],
          ])
          ->add('stockLongDescription', TextType::class, [
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stockLongDescription',
            ],
          ])
          ->add('save', ButtonType::class, [
            'label' => 'Lagerlayout anlegen',
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

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
          'data_class' => StockLayout::class,
        ]);
    }
}
