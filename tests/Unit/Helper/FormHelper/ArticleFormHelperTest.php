<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\Article;
use WebWMS\Form\Article\AddArticleType;
use WebWMS\Form\Article\DeleteArticleType;
use WebWMS\Form\Article\EditArticleType;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Helper\FormHelper\ArticleFormHelper;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Helper\FormHelper',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'ArticleFormHelperTest'
)]
#[CoversClass(ArticleFormHelper::class)]
final class ArticleFormHelperTest extends TestCase
{
    public function testCreateForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $type = 'SomeType';
        $data = null;
        $options = [];

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with($type, $data, $options)
            ->willReturn($formInterface);

        $articleFormHelper = new ArticleFormHelper($formFactory);
        $form = $articleFormHelper->createForm($type, $data, $options);

        self::assertSame($formInterface, $form);
    }

    public function testAddArticleForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(AddArticleType::class)
            ->willReturn($formInterface);

        $articleFormHelper = new ArticleFormHelper($formFactory);
        $form = $articleFormHelper->addArticleForm();

        self::assertSame($formInterface, $form);
    }

    public function testEditArticleForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $article = $this->createMock(Article::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(EditArticleType::class, $article)
            ->willReturn($formInterface);

        $articleFormHelper = new ArticleFormHelper($formFactory);
        $form = $articleFormHelper->editArticleForm($article);

        self::assertSame($formInterface, $form);
    }

    public function testDeleteArticleForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $article = $this->createMock(Article::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(DeleteArticleType::class, $article)
            ->willReturn($formInterface);

        $articleFormHelper = new ArticleFormHelper($formFactory);
        $form = $articleFormHelper->deleteArticleForm($article);

        self::assertSame($formInterface, $form);
    }
}
