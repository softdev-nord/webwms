<?php

declare(strict_types=1);

namespace WebWMS\Form\TransportRequest;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\TransportRequest;

/**
 * @package:    WebWMS\Form\TransportRequest
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        AddTransportRequestType
 */
class AddTransportRequestType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'id',
                    'data-type' => 'id',
                ],
            ])
            ->add('suId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'suId',
                    'data-type' => 'suId',
                ],
            ])
            ->add('trNr', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'trNr',
                    'data-type' => 'trNr',
                ],
            ])
            ->add('trPos', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'trPos',
                    'data-type' => 'trPos',
                ],
            ])
            ->add('trPrio', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'trPrio',
                    'data-type' => 'trPrio',
                ],
            ])
            ->add('articleNr', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'articleNr',
                    'data-type' => 'articleNr',
                ],
            ])
            ->add('trQuantity', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'trQuantity',
                    'data-type' => 'trQuantity',
                ],
            ])
            ->add('stockCoordinate', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'stockCoordinate',
                    'data-type' => 'stockCoordinate',
                ],
            ])
            ->add('stockNr', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'stockNr',
                    'data-type' => 'stockNr',
                ],
            ])
            ->add('stockLevel1', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'stockLevel1',
                    'data-type' => 'stockLevel1',
                ],
            ])
            ->add('stockLevel2', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'stockLevel2',
                    'data-type' => 'stockLevel2',
                ],
            ])
            ->add('stockLevel3', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'stockLevel3',
                    'data-type' => 'stockLevel3',
                ],
            ])
            ->add('stockLevel4', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'stockLevel4',
                    'data-type' => 'stockLevel4',
                ],
            ])
            ->add('trAccess', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'trAccess',
                    'data-type' => 'trAccess',
                ],
                'data' => new \DateTime('NOW', new \DateTimeZone('Europe/Berlin')),
            ])
            ->add('trDispatch', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'trDispatch',
                    'data-type' => 'trDispatch',
                ],
            ])
            ->add('trState', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'trState',
                    'data-type' => 'trState',
                ],
            ])
            ->add('orderUsername', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'orderUsername',
                    'data-type' => 'orderUsername',
                ],
            ])
            ->add('bookingMethod', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'bookingMethod',
                    'data-type' => 'bookingMethod',
                ],
            ])
            ->add('docId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'docId',
                    'data-type' => 'docId',
                ],
            ])
            ->add('orderNr', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'orderNr',
                    'data-type' => 'orderNr',
                ],
            ])
            ->add('orderPos', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'orderPos',
                    'data-type' => 'orderPos',
                ],
            ])
            ->add('charge', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'charge',
                    'data-type' => 'charge',
                ],
            ])
            ->add('loadingEquipment', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'loadingEquipment',
                    'data-type' => 'loadingEquipment',
                ],
            ])
            ->add('confirmationState', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'confirmationState',
                    'data-type' => 'confirmationState',
                ],
            ])
            ->add('trUsername', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'trUsername',
                    'data-type' => 'trUsername',
                ],
            ])
            ->add('trComputerIp', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'trComputerIp',
                    'data-type' => 'trComputerIp',
                ],
            ])
            ->add('trBlocked', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'trBlocked',
                    'data-type' => 'trBlocked',
                ],
            ])
            ->add('trStartDate', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'trStartDate',
                    'data-type' => 'trStartDate',
                ],
                'data' => new \DateTime('NOW', new \DateTimeZone('Europe/Berlin')),
            ])
            ->add('trEdited', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'trEdited',
                    'data-type' => 'trEdited',
                ],
            ])
            ->add('trType', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'trType',
                    'data-type' => 'trType',
                ],
            ])
            ->add('createdAt', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'createdAt',
                    'data-type' => 'createdAt',
                ],
            ])
            ->add('updatedAt', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'updatedAt',
                    'data-type' => 'updatedAt',
                ],
                'data' => null,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TransportRequest::class,
        ]);
    }
}
