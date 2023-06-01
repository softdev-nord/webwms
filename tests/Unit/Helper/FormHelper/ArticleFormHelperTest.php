<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\Article;
use WebWMS\Form\Article\AddArticleType;
use WebWMS\Form\Article\DeleteArticleType;
use WebWMS\Form\Article\EditArticleType;
use WebWMS\Helper\FormHelper\ArticleFormHelper;

/**
 * @package:    WebWMS\Tests\Unit\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ArticleFormHelperTest
 *
 * @covers \WebWMS\Helper\FormHelper\ArticleFormHelper
 */
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
            ->expects(self::once())
            ->method('create')
            ->with($type, $data, $options)
            ->willReturn($formInterface);

        $helper = new ArticleFormHelper($formFactory);
        $result = $helper->createForm($type, $data, $options);

        self::assertSame($formInterface, $result);
    }

    public function testAddArticleForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(AddArticleType::class)
            ->willReturn($formInterface);

        $helper = new ArticleFormHelper($formFactory);
        $result = $helper->addArticleForm();

        self::assertSame($formInterface, $result);
    }

    public function testEditArticleForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $article = $this->createMock(Article::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(EditArticleType::class, $article)
            ->willReturn($formInterface);

        $helper = new ArticleFormHelper($formFactory);
        $result = $helper->editArticleForm($article);

        self::assertSame($formInterface, $result);
    }

    public function testDeleteArticleForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $article = $this->createMock(Article::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(DeleteArticleType::class, $article)
            ->willReturn($formInterface);

        $helper = new ArticleFormHelper($formFactory);
        $result = $helper->deleteArticleForm($article);

        self::assertSame($formInterface, $result);
    }
}
