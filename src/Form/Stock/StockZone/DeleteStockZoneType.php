<?php

declare(strict_types=1);

namespace WebWMS\Form\Stock\StockZone;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockZone;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Form\Stock\StockZone',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2013, SoftDev Nord',
    class: 'DeleteStockZoneType'
)]
class DeleteStockZoneType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
          ->add('id', HiddenType::class, [
            'label' => false,
            'attr' => [
              'class' => 'form-control',
              'id' => 'id',
              'data-type' => 'id',
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

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
          'data_class' => StockZone::class,
        ]);
    }
}
