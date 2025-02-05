<?php

declare(strict_types=1);

namespace WebWMS\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use WebWMS\Entity\User;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Form\User',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'AddUserType'
)]
class AddUserType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
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
            ->add('role', ChoiceType::class, [
                'empty_data' => '',
                'label' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
                'choices' => [
                    'Superadministrator' => 'ROLE_SUPER_ADMIN',
                    'Administrator' => 'ROLE_ADMIN',
                    'Leitung Logistik' => 'ROLE_LOGISTICS_MANAGER',
                    'Leitung Lager' => 'ROLE_WAREHOUSE_MANAGER',
                    'Teamleiter Wareneingang' => 'ROLE_TEAMLEAD_STOCK_IN',
                    'Teamleiter Nachschub' => 'ROLE_TEAMLEAD_REPLENISHMENT',
                    'Teamleiter Warenausgang' => 'ROLE_TEAMLEAD_STOCK_OUT',
                    'Teamleiter Kommissionierung' => 'ROLE_TEAMLEAD_ORDER_PICKING',
                    'Teamleiter Versand' => 'ROLE_TEAMLEAD_SHIPPING',
                    'Mitarbeiter Wareneingang' => 'ROLE_EMPLOYEE_STOCK_IN',
                    'Mitarbeiter Nachschub' => 'ROLE_EMPLOYE_REPLENISHMENT',
                    'Mitarbeiter Warenausgang' => 'ROLE_EMPLOYE_STOCK_OUT',
                    'Mitarbeiter Kommissionierung' => 'ROLE_EMPLOYE_ORDER_PICKING',
                    'Mitarbeiter Versand' => 'ROLE_EMPLOYE_SHIPPING',
                ],
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
            'data_class' => User::class,
        ]);
    }
}
