<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Article;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Article;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\Article\ArticleService;
use WebWMS\Service\DataHandlers\Article\ArticleDataHandler;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service\Article',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'ArticleServiceTest'
)]
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
            ->expects($this->once())
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
        $this->mockObject
            ->expects($this->once())
            ->method('getArticleById')
            ->with($articleId)
            ->willReturn($article);

        $result = $this->articleService->getArticleById($articleId);

        self::assertSame($article, $result);
    }

    public function testGetArticleByNrReturnsNullWhenArticleDoesNotExist(): void
    {
        $articleNr = '60000';
        $this->mockObject
            ->expects($this->once())
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
        $this->mockObject
            ->expects($this->once())
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
        $this->mockObject
            ->expects($this->once())
            ->method('getAllArticlesWithJoin')
            ->willReturn(new JsonResponse($articles));

        $jsonResponse = $this->articleService->getAllArticles();

        self::assertInstanceOf(JsonResponse::class, $jsonResponse);
    }

    public function testGetArticle(): void
    {
        $article = new Article();
        $articleNrInput = '123';
        $this->mockObject
            ->expects($this->once())
            ->method('getArticle')
            ->willReturn(new JsonResponse($article));

        $jsonResponse = $this->articleService->getArticle($articleNrInput);

        self::assertInstanceOf(JsonResponse::class, $jsonResponse);
    }

    public function testAddArticle(): void
    {
        $article = new Article();
        $this->mockObject
            ->expects($this->once())
            ->method('addArticle')
            ->with($article);

        $this->articleService->addArticle($article);
    }

    public function testUpdateArticle(): void
    {
        $article = new Article();
        $this->mockObject
            ->expects($this->once())
            ->method('updateArticle')
            ->with($article);

        $this->articleService->updateArticle($article);
    }

    public function testDeleteArticle(): void
    {
        $article = new Article();
        $this->mockObject
            ->expects($this->once())
            ->method('deleteArticle')
            ->with($article);

        $this->articleService->deleteArticle($article);
    }

    public function testGetLastArticleReturnsArticle(): void
    {
        $article = new Article();
        $this->mockObject
            ->expects($this->once())
            ->method('getLastArticle')
            ->willReturn($article);

        $result = $this->articleService->getLastArticle();

        self::assertSame($article, $result);
    }
}
