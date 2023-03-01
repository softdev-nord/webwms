<?php

declare(strict_types=1);

namespace WebWMS\Form\User;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use WebWMS\Entity\Role;
use WebWMS\Entity\User;

/**
 * @package:    WebWMS\Form
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        AddUserType
 */
class AddUserType extends AbstractType
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
            ->add('firstname', TextType::class, [
                'empty_data' => [],
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
            ->add('role', EntityType::class, [
                'empty_data' => '',
                'label' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
                'class' => Role::class,
                'choice_label' => function ($role) {
                    return $role->getName();
                },
            ])
            ->add('password', RepeatedType::class, [
                'empty_data' => '',
                'invalid_message' => 'Die Passwortfelder müssen übereinstimmen.',
                'options' => ['attr' => ['class' => 'form-control']],
                'required' => true,
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => false,
                ],
                'second_options' => [
                    'label' => false,
                ],
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Bitte ein Passwort eingeben',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Das Passwort sollte mindestens {{ limit }} Zeichen lang sein.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('save', ButtonType::class, [
                'label' => 'Benutzer anlegen',
                'attr' => [
                    'class' => 'btn btn-lg',
                ],
            ])
            ->add('back_to_user_overview', ButtonType::class, [
                'label' => 'Zurück zur Übersicht',
                'attr' => [
                    'class' => 'btn btn-lg',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
