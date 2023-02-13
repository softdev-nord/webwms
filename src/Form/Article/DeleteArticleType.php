<?php

declare(strict_types=1);

namespace WebWMS\Form\Article;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Article;

/**
 * @package:    WebWMS\Form\Article
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        DeleteArticleType
 */
class DeleteArticleType extends AbstractType
{
    /**
     * @SuppressWarnings("unused")
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('articleId', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'articleNr',
                    'data-type' => 'articleNr',
                ],
            ])
            ->add('delete', ButtonType::class, [
                'label' => 'Löschen',
                'attr' => [
                    'class' => 'btn btn-lg',
                ],
            ])
            ->add('abort', ButtonType::class, [
                'label' => 'Abbrechen',
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
