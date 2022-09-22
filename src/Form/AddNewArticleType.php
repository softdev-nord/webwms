<?php

namespace WebWMS\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Article;

/**
 * @package:    WebWMS\Form
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        AddNewArticleType
 */
class AddNewArticleType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('article_id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'article_id',
                    'data-type' => 'article_id',
                ],
            ])
            ->add('article_nr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'article_nr',
                    'data-type' => 'article_nr',
                    'style' => 'background-color: transparent',
                ],
            ])
            ->add('article_name', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'article_name',
                    'data-type' => 'article_name',
                ],
            ])
            ->add('article_category', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'article_category',
                    'data-type' => 'article_category',
                ],
            ])
            ->add('article_weight', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'article_weight',
                    'data-type' => 'article_weight',
                ],
            ])
            ->add('article_ean', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'article_ean',
                    'data-type' => 'article_ean',
                ],
            ])
            ->add('article_unit', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'article_unit',
                    'data-type' => 'article_unit',
                ],
            ])
            ->add('article_depth', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'article_depth',
                    'data-type' => 'article_depth',
                ],
            ])
            ->add('article_width', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'article_width',
                    'data-type' => 'article_width',
                ],
            ])
            ->add('article_height', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'article_height',
                    'data-type' => 'article_height',
                ],
            ])
            ->add('stock_out_strategy', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'FIFO (First In – First Out)' => 'FIFO',
                    'FEFO (First Expired – First Out)' => 'FEFO',
                    'LIFO (Last In – First Out)' => 'LIFO',
                    'HIFO (Highest In – First Out)' => 'HIFO',
                    'LOFO (Lowest In – First Out)' => 'LOFO',
                    'Chaotische Lagerhaltung (Chaotic warehousing)' => 'CWH',
                ],
            ])
            ->add('le_quantity', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'le_quantity	',
                    'data-type' => 'le_quantity',
                ],
            ])
            ->add('add_article', SubmitType::class, [
                'label' => 'Artikel anlegen',
                'attr' => [
                    'class' => 'btn btn-secondary btn-lg',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
