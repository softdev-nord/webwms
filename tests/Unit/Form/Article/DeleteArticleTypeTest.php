<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Article;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Article;
use WebWMS\Form\Article\DeleteArticleType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Article
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        DeleteArticleTypeTest
 *
 * @covers \WebWMS\Form\Article\DeleteArticleType
 */
final class DeleteArticleTypeTest extends TestCase
{
    private DeleteArticleType $deleteArticleType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->deleteArticleType = new DeleteArticleType();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->deleteArticleType);
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
                        'id' => 'articleNr',
                        'data-type' => 'articleNr',
                    ],
                ]],
                ['save', SubmitType::class, [
                    'label' => 'Löschen',
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

        $form = $this->deleteArticleType;
        $form->buildForm($builder, (array) $optionsResolver);
    }

    public function testConfigureOptions(): void
    {
        $deleteArticleType = $this->deleteArticleType;
        $resolver = $this->createMock(OptionsResolver::class);

        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with([
                    'data_class' => Article::class,
                ]
            );

        $deleteArticleType->configureOptions($resolver);
    }
}
