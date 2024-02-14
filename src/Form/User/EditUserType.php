<?php

declare(strict_types=1);

namespace WebWMS\Form\User;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\UserEntity;
use WebWMS\Entity\UserRoleEntity;

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
    #[Override]
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
            ->add('role', ChoiceType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'class' => UserRoleEntity::class,
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

    #[Override]
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => UserEntity::class,
        ]);
    }
}
