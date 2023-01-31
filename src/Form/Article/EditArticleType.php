<?php

declare(strict_types=1);

namespace WebWMS\Form\Article;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use WebWMS\Entity\Article;

/**
 * @package:    WebWMS\Form
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        EditArticleType
 */
class EditArticleType extends AbstractType
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker
    ) {
    }

    /**
     * @SuppressWarnings("unused")
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('articleId', HiddenType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleId',
                'data-type' => 'articleId',
            ],
        ]);
        if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $builder
                ->add('articleNr', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'articleNr',
                        'data-type' => 'articleNr',
                        'style' => 'background-color: transparent',
                        'readonly' => 'readonly',
                    ],
                ]);
        } else {
            $builder->add('articleNr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'articleNr',
                    'data-type' => 'articleNr',
                    'style' => 'background-color: transparent',
                ],
            ]);
        }
        $builder->add('articleName', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleName',
                'data-type' => 'articleName',
            ],
        ]);
        $builder->add('articleCategory', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleCategory',
                'data-type' => 'articleCategory',
            ],
        ]);
        $builder->add('articleWeight', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleWeight',
                'data-type' => 'articleWeight',
            ],
        ]);
        $builder->add('articleEan', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleEan',
                'data-type' => 'articleEan',
            ],
        ]);
        $builder->add('articleUnit', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleUnit',
                'data-type' => 'articleUnit',
            ],
        ]);
        $builder->add('articleDepth', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleDepth',
                'data-type' => 'articleDepth',
            ],
        ]);
        $builder->add('articleWidth', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleWidth',
                'data-type' => 'articleWidth',
            ],
        ]);
        $builder->add('articleHeight', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleHeight',
                'data-type' => 'articleHeight',
            ],
        ]);
        $builder->add('stockOutStrategy', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'stockOutStrategy',
                'data-type' => 'stockOutStrategy',
            ],
        ]);
        $builder->add('leQuantity', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'leQuantity	',
                'data-type' => 'leQuantity',
            ],
        ]);
        $builder->add('standardLoadingEquipment', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'standardLoadingEquipment	',
                'data-type' => 'standardLoadingEquipment',
            ],
        ]);
        $builder->add('save', SubmitType::class, [
            'label' => 'Änderungen speichern',
            'attr' => [
                'class' => 'btn btn-lg',
            ],
        ]);
        $builder->add('back_to_article_overview', ButtonType::class, [
            'label' => 'Zurück zur Übersicht',
            'attr' => [
                'class' => 'btn btn-lg',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
