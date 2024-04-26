<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Article;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\ArticleEntity;
use WebWMS\Service\Article\ArticleService;
use WebWMS\Service\DataHandlers\Article\ArticleDataHandler;

/**
 * @package:    WebWMS\Tests\Unit\Service\ArticleController
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        ArticleServiceTest
 */
#[CoversClass(ArticleService::class)]
final class ArticleServiceTest extends TestCase
{
    private MockObject $mockObject;

    private ArticleService $articleService;

    protected function setUp(): void
    {
        $this->mockObject = $this->createMock(ArticleDataHandler::class);
        $this->articleService = new ArticleService($this->mockObject);
    }

    public function testGetArticleByIdReturnsNullWhenArticleDoesNotExist(): void
    {
        $articleId = 1;
        $this->mockObject
            ->expects(self::once())
            ->method('getArticleById')
            ->with($articleId)
            ->willReturn(null);

        $result = $this->articleService->getArticleById($articleId);

        self::assertNull($result);
    }

    public function testGetArticleByIdReturnsArticleWhenArticleExists(): void
    {
        $articleId = 1;
        $articleEntity = new ArticleEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('getArticleById')
            ->with($articleId)
            ->willReturn($articleEntity);

        $result = $this->articleService->getArticleById($articleId);

        self::assertSame($articleEntity, $result);
    }

    public function testGetArticleByNrReturnsNullWhenArticleDoesNotExist(): void
    {
        $articleNr = '60000';
        $this->mockObject
            ->expects(self::once())
            ->method('getArticleByNr')
            ->with($articleNr)
            ->willReturn(null);

        $result = $this->articleService->getArticleByNr($articleNr);

        self::assertNull($result);
    }

    public function testGetArticleByNrReturnsArticleWhenArticleExists(): void
    {
        $articleNr = '60000';
        $articleEntity = new ArticleEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('getArticleByNr')
            ->with($articleNr)
            ->willReturn($articleEntity);

        $result = $this->articleService->getArticleByNr($articleNr);

        self::assertSame($articleEntity, $result);
    }

    public function testGetAllArticlesReturnsJsonResponse(): void
    {
        $articles = [
            new ArticleEntity(),
            new ArticleEntity(),
        ];
        $this->mockObject
            ->expects(self::once())
            ->method('getAllArticlesWithJoin')
            ->willReturn(new JsonResponse($articles));

        $result = $this->articleService->getAllArticles();

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testGetArticle(): void
    {
        $articleEntity = new ArticleEntity();
        $articleNrInput = '123';
        $this->mockObject
            ->expects(self::once())
            ->method('getArticle')
            ->willReturn(new JsonResponse($articleEntity));

        $result = $this->articleService->getArticle($articleNrInput);

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testAddArticle(): void
    {
        $articleEntity = new ArticleEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('addArticle')
            ->with($articleEntity);

        $this->articleService->addArticle($articleEntity);
    }

    public function testUpdateArticle(): void
    {
        $articleEntity = new ArticleEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('updateArticle')
            ->with($articleEntity);

        $this->articleService->updateArticle($articleEntity);
    }

    public function testDeleteArticle(): void
    {
        $articleEntity = new ArticleEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('deleteArticle')
            ->with($articleEntity);

        $this->articleService->deleteArticle($articleEntity);
    }

    public function testGetLastArticleReturnsArticle(): void
    {
        $articleEntity = new ArticleEntity();
        $this->mockObject
            ->expects(self::once())
            ->method('getLastArticle')
            ->willReturn($articleEntity);

        $result = $this->articleService->getLastArticle();

        self::assertSame($articleEntity, $result);
    }
}
