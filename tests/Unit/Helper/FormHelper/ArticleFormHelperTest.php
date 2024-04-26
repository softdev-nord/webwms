<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\ArticleEntity;
use WebWMS\Form\Article\AddArticleType;
use WebWMS\Form\Article\DeleteArticleType;
use WebWMS\Form\Article\EditArticleType;
use WebWMS\Helper\FormHelper\ArticleFormHelper;

/**
 * @package:    WebWMS\Tests\Unit\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        ArticleFormHelperTest
 */
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
            ->expects(self::once())
            ->method('create')
            ->with($type, $data, $options)
            ->willReturn($formInterface);

        $articleFormHelper = new ArticleFormHelper($formFactory);
        $result = $articleFormHelper->createForm($type, $data, $options);

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

        $articleFormHelper = new ArticleFormHelper($formFactory);
        $result = $articleFormHelper->addArticleForm();

        self::assertSame($formInterface, $result);
    }

    public function testEditArticleForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $article = $this->createMock(ArticleEntity::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(EditArticleType::class, $article)
            ->willReturn($formInterface);

        $articleFormHelper = new ArticleFormHelper($formFactory);
        $result = $articleFormHelper->editArticleForm($article);

        self::assertSame($formInterface, $result);
    }

    public function testDeleteArticleForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $article = $this->createMock(ArticleEntity::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(DeleteArticleType::class, $article)
            ->willReturn($formInterface);

        $articleFormHelper = new ArticleFormHelper($formFactory);
        $result = $articleFormHelper->deleteArticleForm($article);

        self::assertSame($formInterface, $result);
    }
}
