<?php

declare(strict_types=1);

namespace WebWMS\Form\Article;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
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
class AddArticleType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('articleId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'articleId',
                    'data-type' => 'articleId',
                ],
            ])
            ->add('articleNr', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'articleNr',
                    'data-type' => 'articleNr',
                ],
            ])
            ->add('articleName', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'articleName',
                    'data-type' => 'articleName',
                ],
            ])
            ->add('articleCategory', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'articleCategory',
                    'data-type' => 'articleCategory',
                ],
            ])
            ->add('articleWeight', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'articleWeight',
                    'data-type' => 'articleWeight',
                ],
            ])
            ->add('articleEan', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'articleEan',
                    'data-type' => 'articleEan',
                ],
            ])
            ->add('articleUnit', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'articleUnit',
                    'data-type' => 'articleUnit',
                ],
            ])
            ->add('articleDepth', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'articleDepth',
                    'data-type' => 'articleDepth',
                ],
            ])
            ->add('articleWidth', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'articleWidth',
                    'data-type' => 'articleWidth',
                ],
            ])
            ->add('articleHeight', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'article_height',
                    'data-type' => 'article_height',
                ],
            ])
            ->add('stockOutStrategy', ChoiceType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-select',
                ],
                'choices' => [
                    'FIFO (First In – First Out)' => 'FIFO',
                    'FEFO (First Expired – First Out)' => 'FEFO',
                    'LIFO (Last In – First Out)' => 'LIFO',
                    'HIFO (Highest In – First Out)' => 'HIFO',
                    'LOFO (Lowest In – First Out)' => 'LOFO',
                    'Chaotische Lagerhaltung (Chaotic warehousing)' => 'CWH',
                ],
            ])
            ->add('standardLoadingEquipment', ChoiceType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-select',
                ],
                'choices' => [
                    'KARTON' => 'KARTON',
                    'PALETTE' => 'PALETTE',
                    'BLOCK' => 'BLOCK',
                ],
            ])
            ->add('leQuantity', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'leQuantity	',
                    'data-type' => 'leQuantity',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Artikel anlegen',
                'attr' => [
                    'class' => 'btn btn-lg',
                ],
            ])
            ->add('back_to_article_overview', ButtonType::class, [
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
            'data_class' => Article::class,
        ]);
    }
}
