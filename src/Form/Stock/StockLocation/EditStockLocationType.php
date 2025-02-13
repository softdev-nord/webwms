<?php

declare(strict_types=1);

namespace WebWMS\Form\Stock\StockLocation;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLocation;
use WebWMS\Entity\StockZone;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Form\Stock\StockLocation',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'EditStockLocationType'
)]
class EditStockLocationType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
          ->add('stockLocationLn', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stockLocationLn',
            ],
          ])
          ->add('stockLocationFb', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stockLocationFb',
            ],
          ])
          ->add('stockLocationSp', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stockLocationSp',
            ],
          ])
          ->add('stockLocationTf', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stockLocationTf',
            ],
          ])
          ->add('stockLocationCoordinate', HiddenType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stockLocationCoordinate',
            ],
          ])
          ->add('stockLocationDesc', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stockLocationDesc',
            ],
          ])
          ->add('stockLocationWidth', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stockLocationWidth',
            ],
          ])
          ->add('stockLocationDepth', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stockLocationDepth',
            ],
          ])
          ->add('stockLocationHeight', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'data-type' => 'stock_location_height',
            ],
          ])
          ->add('stockLocationZone', EntityType::class, [
            'empty_data' => '',
            'label' => false,
            'class' => StockZone::class,
            'choice_label' => 'stockZoneShortDesc',
            'mapped' => false,
            'attr' => [
              'class' => 'form-control',
            ],
          ])
          ->add('save', SubmitType::class, [
            'label' => 'Änderungen speichern',
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
