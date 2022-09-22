<?php

namespace WebWMS\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
        $builder->add('article_id', HiddenType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'article_id',
                'data-type' => 'article_id',
            ],
        ]);
        if (!$this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $builder
                ->add('article_nr', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'article_nr',
                        'data-type' => 'article_nr',
                        'style' => 'background-color: transparent',
                        'readonly' => 'readonly',
                    ],
                ]);
        } else {
            $builder->add('article_nr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'article_nr',
                    'data-type' => 'article_nr',
                    'style' => 'background-color: transparent',
                ],
            ]);
        }
        $builder->add('article_name', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'article_name',
                'data-type' => 'article_name',
            ],
        ]);
        $builder->add('article_category', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'article_category',
                'data-type' => 'article_category',
            ],
        ]);
        $builder->add('article_weight', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'article_weight',
                'data-type' => 'article_weight',
            ],
        ]);
        $builder->add('article_ean', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'article_ean',
                'data-type' => 'article_ean',
            ],
        ]);
        $builder->add('article_unit', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'article_unit',
                'data-type' => 'article_unit',
            ],
        ]);
        $builder->add('article_depth', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'article_depth',
                'data-type' => 'article_depth',
            ],
        ]);
        $builder->add('article_width', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'article_width',
                'data-type' => 'article_width',
            ],
        ]);
        $builder->add('article_height', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'article_height',
                'data-type' => 'article_height',
            ],
        ]);
        $builder->add('stock_out_strategy', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'stock_out_strategy',
                'data-type' => 'stock_out_strategy',
            ],
        ]);
        $builder->add('le_quantity', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'le_quantity	',
                'data-type' => 'le_quantity',
            ],
        ]);
        $builder->add('standard_loading_equipment', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'standard_loading_equipment	',
                'data-type' => 'standard_loading_equipment',
            ],
        ]);
        $builder->add('save', ButtonType::class, [
            'label' => 'Änderungen speichern',
            'attr' => [
                'class' => 'btn btn-secondary btn-lg',
            ],
        ]);
        $builder->add('back_to_article_overview', ButtonType::class, [
            'label' => 'Zurück zur Übersicht',
            'attr' => [
                'class' => 'btn btn-secondary btn-lg',
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
