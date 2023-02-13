<?php

declare(strict_types=1);

namespace WebWMS\Form\Customer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Customer;

/**
 * @package:    WebWMS\Form
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        DeleteCustomerType
 */
class DeleteCustomerType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerNr', HiddenType::class, [
                'attr' => [
                    'id' => 'customerNr',
                    'data-type' => 'customerNr',
                ],
            ])
            ->add('delete', ButtonType::class, [
                'label' => 'Löschen',
                'attr' => [
                    'class' => 'btn btn-lg',
                ],
            ])
            ->add('abort', ButtonType::class, [
                'label' => 'Abbrechen',
                'attr' => [
                    'class' => 'btn btn-lg',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
