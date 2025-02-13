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
use WebWMS\Entity\Article;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Form\Article',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'EditArticleType'
)]
class EditArticleType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     * @SuppressWarnings(ElseExpression)
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
        ])
        ->add('articleNr', TextType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleNr',
                'data-type' => 'articleNr',
                'style' => 'background-color: transparent',
            ],
        ])
        ->add('articleName', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleName',
                'data-type' => 'articleName',
            ],
        ])
        ->add('articleCategory', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleCategory',
                'data-type' => 'articleCategory',
            ],
        ])
        ->add('articleWeight', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleWeight',
                'data-type' => 'articleWeight',
            ],
        ])
        ->add('articleEan', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleEan',
                'data-type' => 'articleEan',
            ],
        ])
        ->add('articleUnit', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleUnit',
                'data-type' => 'articleUnit',
            ],
        ])
        ->add('articleDepth', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleDepth',
                'data-type' => 'articleDepth',
            ],
        ])
        ->add('articleWidth', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleWidth',
                'data-type' => 'articleWidth',
            ],
        ])
        ->add('articleHeight', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'articleHeight',
                'data-type' => 'articleHeight',
            ],
        ])
        ->add('stockOutStrategy', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'stockOutStrategy',
                'data-type' => 'stockOutStrategy',
            ],
        ])
        ->add('leQuantity', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'leQuantity	',
                'data-type' => 'leQuantity',
            ],
        ])
        ->add('standardLoadingEquipment', TextType::class, [
            'empty_data' => '',
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'id' => 'standardLoadingEquipment	',
                'data-type' => 'standardLoadingEquipment',
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
            'data_class' => Article::class,
        ]);
    }
}
