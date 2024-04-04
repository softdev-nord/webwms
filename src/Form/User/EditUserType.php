<?php

declare(strict_types=1);

namespace WebWMS\Form\User;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\UserEntity;
use WebWMS\Entity\UserGroupEntity;
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
            ->add('email', TextType::class, [
                'empty_data' => '',
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ])
            ->add('role', ChoiceType::class, [
                'empty_data' => '',
                'mapped' => false,
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'data_class' => UserRoleEntity::class,
                'choices' => [
                    'Superadministrator' => 'ROLE_SUPER_ADMIN',
                    'Administrator' => 'ROLE_ADMIN',
                    'Superbenutzer' => 'ROLE_SUPER_USER',
                    'Benutzer' => 'ROLE_USER',
                    'Leitung Logistik' => 'ROLE_LOGISTICS_MANAGER',
                    'Leitung Lager' => 'ROLE_WAREHOUSE_MANAGER',
                    'Teamleitung Wareneingang' => 'ROLE_TEAMLEAD_STOCK_IN',
                    'Teamleitung Nachschub' => 'ROLE_TEAMLEAD_REPLENISHMENT',
                    'Teamleitung Warenausgang' => 'ROLE_TEAMLEAD_STOCK_OUT',
                    'Teamleitung Kommissionierung' => 'ROLE_TEAMLEAD_ORDER_PICKING',
                    'Teamleitung Versand' => 'ROLE_TEAMLEAD_SHIPPING',
                    'Teamleitung Leitstand' => 'ROLE_TEAMLEAD_CONTROL_CENTRE',
                    'Mitarbeiter Wareneingang' => 'ROLE_EMPLOYEE_STOCK_IN',
                    'Mitarbeiter Nachschub' => 'ROLE_EMPLOYE_REPLENISHMENT',
                    'Mitarbeiter Warenausgang' => 'ROLE_EMPLOYE_STOCK_OUT',
                    'Mitarbeiter Kommissionierung' => 'ROLE_EMPLOYE_ORDER_PICKING',
                    'Mitarbeiter Versand' => 'ROLE_EMPLOYE_SHIPPING',
                    'Mitarbeiter Leitstand' => 'ROLE_TEAMLEAD_CONTROL_CENTRE',
                ],
            ])
            ->add('userGroups', ChoiceType::class, [
                'mapped' => true,
                'multiple' => true,
                'expanded' => true,
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'data_class' => UserGroupEntity::class,
                'choices' => [
                    'Administratoren' => 'GROUP_SUPER_ADMIN',
                    'Leitung Logistik' => 'GROUP_LOGISTICS_MANAGER',
                    'Leitung Lager' => 'GROUP_WAREHOUSE_MANAGER',
                    'Teamleitung Wareneingang' => 'GROUP_TEAMLEAD_STOCK_IN',
                    'Teamleitung Nachschub' => 'GROUP_TEAMLEAD_REPLENISHMENT',
                    'Teamleitung Warenausgang' => 'GROUP_TEAMLEAD_STOCK_OUT',
                    'Teamleitung Kommissionierung' => 'GROUP_TEAMLEAD_ORDER_PICKING',
                    'Teamleitung Versand' => 'GROUP_TEAMLEAD_SHIPPING',
                    'Teamleitung Leitstand' => 'GROUP_TEAMLEAD_CONTROL_CENTRE',
                    'Mitarbeiter Wareneingang' => 'GROUP_EMPLOYEE_STOCK_IN',
                    'Mitarbeiter Nachschub' => 'GROUP_EMPLOYE_REPLENISHMENT',
                    'Mitarbeiter Warenausgang' => 'GROUP_EMPLOYE_STOCK_OUT',
                    'Mitarbeiter Kommissionierung' => 'GROUP_EMPLOYE_ORDER_PICKING',
                    'Mitarbeiter Versand' => 'GROUP_EMPLOYE_SHIPPING',
                    'Mitarbeiter Leitstand' => 'GROUP_TEAMLEAD_CONTROL_CENTRE',
                ],
            ])
            ->add('userGroups', EntityType::class, [
                'class' => UserGroupEntity::class,
                'query_builder' => static fn (EntityRepository $er): QueryBuilder => $er->createQueryBuilder('uge')
                    ->orderBy('uge.description', 'ASC'),
                'choice_label' => 'description',
                'mapped' => false,
                'multiple' => true,
                'expanded' => true,
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserEntity::class,
        ]);
    }
}
