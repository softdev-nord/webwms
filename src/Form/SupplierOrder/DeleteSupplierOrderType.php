<?php

declare(strict_types=1);

namespace WebWMS\Form\SupplierOrder;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Form\SupplierOrder',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'DeleteSupplierOrderType'
)]
class DeleteSupplierOrderType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     * @SuppressWarnings(ElseExpression)
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
          ->add('supplierOrderId', HiddenType::class, [
            'label' => false,
            'attr' => [
              'class' => 'form-control',
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
          'data_class' => SupplierOrder::class,
        ]);
    }
}
