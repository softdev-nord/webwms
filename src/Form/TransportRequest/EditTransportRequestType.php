<?php

namespace WebWMS\Form\TransportRequest;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\TransportRequestEntity;

class EditTransportRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var TransportRequestEntity $formData */
        $formData = $builder->getForm()->getData();

        $builder
            ->add('suId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('trNr', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('trPos', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('trPrio', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('articleNr', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('trQuantity', NumberType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('stockCoordinate', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('stockNr', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('stockLevel1', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('stockLevel2', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('stockLevel3', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('stockLevel4', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'id' => 'stockLevel4',
                    'data-type' => 'stockLevel4',
                ],
            ])
            ->add('trAccess', DateTimeType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('trDispatch', DateTimeType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('trState', TextType::class, [
                'label' => false,
                'data' => ($formData->getTrState() === 1) ? 'In Bearbeitung' : 'Offen',
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('orderUsername', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('bookingMethod', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('docId', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('orderNr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('orderPos', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('charge', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('loadingEquipment', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('confirmationState', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('trUsername', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('trComputerIp', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('trBlocked', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('trStartDate', DateTimeType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('trEdited', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('trType', TextType::class, [
                'label' => false,
                'data' => ($formData->getTrType() === 1) ? 'Zugang' : 'Abgang',
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('createdAt', DateTimeType::class, [
                'label' => false,
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'dd.MM.yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'background-color: transparent',
                    'readonly' => 'readonly',
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
            'data_class' => TransportRequestEntity::class,
        ]);
    }
}
