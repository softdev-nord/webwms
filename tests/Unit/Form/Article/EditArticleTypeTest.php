<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Article;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Article;
use WebWMS\Form\Article\EditArticleType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Article
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        EditArticleTypeTest
 *
 * @covers \WebWMS\Form\Article\EditArticleType
 */
final class EditArticleTypeTest extends TestCase
{
    private EditArticleType $editArticleType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->editArticleType = new EditArticleType();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->editArticleType);
    }

    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects($this->exactly(1))
            ->method('add')
            ->withConsecutive(
                ['articleId', HiddenType::class, [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'articleId',
                        'data-type' => 'articleId',
                    ],
                ]],
                ['articleNr', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'articleNr',
                        'data-type' => 'articleNr',
                    ],
                ]],
                ['articleName', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'articleName',
                        'data-type' => 'articleName',
                    ],
                ]],
                ['articleCategory', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'articleCategory',
                        'data-type' => 'articleCategory',
                    ],
                ]],
                ['articleWeight', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'articleWeight',
                        'data-type' => 'articleWeight',
                    ],
                ]],
                ['articleEan', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'articleEan',
                        'data-type' => 'articleEan',
                    ],
                ]],
                ['articleUnit', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'articleUnit',
                        'data-type' => 'articleUnit',
                    ],
                ]],
                ['articleDepth', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'articleDepth',
                        'data-type' => 'articleDepth',
                    ],
                ]],
                ['articleWidth', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'articleWidth',
                        'data-type' => 'articleWidth',
                    ],
                ]],
                ['articleHeight', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'article_height',
                        'data-type' => 'article_height',
                    ],
                ]],
                ['stockOutStrategy', ChoiceType::class, [
                    'empty_data' => '',
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
                ]],
                ['standardLoadingEquipment', ChoiceType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-select',
                    ],
                    'choices' => [
                        'KARTON' => 'KARTON',
                        'PALETTE' => 'PALETTE',
                        'BLOCK' => 'BLOCK',
                    ],
                ]],
                ['leQuantity', TextType::class, [
                    'empty_data' => '',
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'id' => 'leQuantity	',
                        'data-type' => 'leQuantity',
                    ],
                ]],
                ['save', SubmitType::class, [
                    'label' => 'Artikel anlegen',
                    'attr' => [
                        'class' => 'btn btn-lg',
                    ],
                ]],
                ['abort', ButtonType::class, [
                    'label' => 'Abbrechen',
                    'attr' => [
                        'class' => 'btn btn-lg abort',
                    ],
                ]],
            );

        $optionsResolver = $this->createMock(OptionsResolver::class);

        $form = $this->editArticleType;
        $form->buildForm($builder, (array) $optionsResolver);
    }

    public function testConfigureOptions(): void
    {
        $addArticleType = $this->editArticleType;
        $resolver = $this->createMock(OptionsResolver::class);

        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with([
                'data_class' => Article::class,
                ]
            );

        $addArticleType->configureOptions($resolver);
    }
}
