<?php

declare(strict_types=1);

namespace WebWMS\Form\User;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Role;
use WebWMS\Entity\User;

/**
 * @package:    WebWMS\Form
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        EditUserType
 */
class EditUserType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('role', EntityType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'class' => Role::class,
                'choice_label' => function ($role) {
                    return $role->getName();
                },
            ])
            ->add('firstname', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('lastname', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('save', ButtonType::class, [
                'label' => 'Änderungen speichern',
                'attr' => [
                    'class' => 'btn btn-lg',
                ],
            ])
            ->add('abort', ButtonType::class, [
                'label' => 'Abbrechen',
                'attr' => [
                    'class' => 'btn btn-lg abort',
                ],
            ])
        ;

        //        $builder->get('roles')
        //            ->addModelTransformer(new CallbackTransformer(
        //                function ($tagsAsArray) {
        //                    // transform the array to a string
        //                    return implode(', ', $tagsAsArray);
        //                },
        //                function ($tagsAsString) {
        //                    // transform the string back to an array
        //                    return explode(', ', $tagsAsString);
        //                }
        //            ))
        //        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
