<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Article;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\Article;
use WebWMS\Service\Article\ArticleService;
use WebWMS\Service\DataHandlers\Article\ArticleDataHandler;

/**
 * @package:    WebWMS\Tests\Unit\Service\Article
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ArticleServiceTest
 *
 * @covers \WebWMS\Service\Article\ArticleService
 */
final class ArticleServiceTest extends TestCase
{
    /**
     * @var (ArticleDataHandler&MockObject)|MockObject
     */
    private MockObject|ArticleDataHandler $articleDataHandler;

    private ArticleService $articleService;

    protected function setUp(): void
    {
        $this->articleDataHandler = $this->createMock(ArticleDataHandler::class);
        $this->articleService = new ArticleService($this->articleDataHandler);
    }

    public function testGetArticleByIdReturnsNullWhenArticleDoesNotExist(): void
    {
        $articleId = 1;
        $this->articleDataHandler
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
        $article = new Article();
        $this->articleDataHandler
            ->expects(self::once())
            ->method('getArticleById')
            ->with($articleId)
            ->willReturn($article);

        $result = $this->articleService->getArticleById($articleId);

        self::assertSame($article, $result);
    }

    public function testGetArticleByNrReturnsNullWhenArticleDoesNotExist(): void
    {
        $articleNr = '60000';
        $this->articleDataHandler
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
        $article = new Article();
        $this->articleDataHandler
            ->expects(self::once())
            ->method('getArticleByNr')
            ->with($articleNr)
            ->willReturn($article);

        $result = $this->articleService->getArticleByNr($articleNr);

        self::assertSame($article, $result);
    }

    public function testGetAllArticlesReturnsJsonResponse(): void
    {
        $articles = [
            new Article(),
            new Article(),
        ];
        $this->articleDataHandler
            ->expects(self::once())
            ->method('getAllArticlesWithJoin')
            ->willReturn(new JsonResponse($articles));

        $result = $this->articleService->getAllArticles();

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testGetArticle(): void
    {
        $article = new Article();
        $request = new Request();
        $this->articleDataHandler
            ->expects(self::once())
            ->method('getArticle')
            ->willReturn(new JsonResponse($article));

        $result = $this->articleService->getArticle($request);

        self::assertInstanceOf(JsonResponse::class, $result);
    }

    public function testAddArticle(): void
    {
        $article = new Article();
        $this->articleDataHandler
            ->expects(self::once())
            ->method('addArticle')
            ->with($article);

        $this->articleService->addArticle($article);
    }

    public function testUpdateArticle(): void
    {
        $article = new Article();
        $this->articleDataHandler
            ->expects(self::once())
            ->method('updateArticle')
            ->with($article);

        $this->articleService->updateArticle($article);
    }

    public function testDeleteArticle(): void
    {
        $article = new Article();
        $this->articleDataHandler
            ->expects(self::once())
            ->method('deleteArticle')
            ->with($article);

        $this->articleService->deleteArticle($article);
    }

    public function testGetLastArticleReturnsArticle(): void
    {
        $article = new Article();
        $this->articleDataHandler
            ->expects(self::once())
            ->method('getLastArticle')
            ->willReturn($article);

        $result = $this->articleService->getLastArticle();

        self::assertSame($article, $result);
    }
}
