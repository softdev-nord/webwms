<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\DataHandlers\Article;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Article;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\Article\ArticleDataHandler;
use WebWMS\Service\DateTimeService;
use WebWMS\Service\Stock\StockOccupancyService;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service\DataHandlers\Article',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'ArticleDataHandlerTest'
)]
#[CoversClass(ArticleDataHandler::class)]
final class ArticleDataHandlerTest extends TestCase
{
    private ArticleDataHandler $articleDataHandler;

    private MockObject $entityManager;

    private MockObject $dateTimeService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->dateTimeService = $this->createMock(DateTimeService::class);
        $stockOccupancyService = $this->createMock(StockOccupancyService::class);

        $this->articleDataHandler = new ArticleDataHandler(
            $this->entityManager,
            $stockOccupancyService,
            $this->dateTimeService
        );
    }

    public function testSave(): void
    {
        $article = $this->createMock(Article::class);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($article);
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->articleDataHandler->save($article);
    }

    public function testDelete(): void
    {
        $article = $this->createMock(Article::class);

        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($article);
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->articleDataHandler->delete($article);
    }

    public function testGetArticleById(): void
    {
        $articleId = 123;
        $expectedArticle = $this->createMock(Article::class);
        $repository = $this->createMock(EntityRepository::class);

        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['articleId' => $articleId])
            ->willReturn($expectedArticle);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Article::class)
            ->willReturn($repository);

        $article = $this->articleDataHandler->getArticleById($articleId);

        self::assertSame($expectedArticle, $article);
    }

    public function testGetArticleByNr(): void
    {
        $articleNr = 'ABC123';
        $expectedArticle = $this->createMock(Article::class);
        $repository = $this->createMock(EntityRepository::class);

        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['articleNr' => $articleNr])
            ->willReturn($expectedArticle);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Article::class)
            ->willReturn($repository);

        $article = $this->articleDataHandler->getArticleByNr($articleNr);

        self::assertSame($expectedArticle, $article);
    }

    public function testGetAllArticles(): void
    {
        $expectedArticles = [$this->createMock(Article::class)];
        $repository = $this->createMock(EntityRepository::class);

        $repository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($expectedArticles);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Article::class)
            ->willReturn($repository);

        $articles = $this->articleDataHandler->getAllArticles();

        self::assertSame($expectedArticles, $articles);
    }

    public function testGetAllArticlesWithJoin(): void
    {
        $expectedData = [['article_id' => 1, 'article_nr' => 'ABC123']];
        $connection = $this->createMock(Connection::class);

        $this->entityManager
            ->expects($this->once())
            ->method('getConnection')
            ->willReturn($connection);

        $connection
            ->expects($this->once())
            ->method('fetchAllAssociative')
            ->with(self::isType('string'))
            ->willReturn($expectedData);

        $jsonResponse = $this->articleDataHandler->getAllArticlesWithJoin();

        self::assertInstanceOf(JsonResponse::class, $jsonResponse);
    }

    public function testGetArticle(): void
    {
        $articleNrInput = '12345';

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);

        $queryBuilder
            ->expects($this->once())
            ->method('select')
            ->willReturnSelf();
        $queryBuilder
            ->expects($this->once())
            ->method('from')
            ->willReturnSelf();
        $queryBuilder
            ->expects($this->once())
            ->method('where')
            ->willReturnSelf();
        $queryBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->willReturnSelf();
        $queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $query
            ->expects($this->once())
            ->method('getArrayResult')
            ->willReturn([
                [
                    'articleId' => 1,
                    'articleNr' => '12345',
                    'articleName' => 'TestArtikel 1',
                    'articleCategory' => 'TestKategorie 1',
                    'articleWeight' => 1300.0,
                    'articleEan' => '123456789',
                    'articleUnit' => 'Stk',
                    'articleDepth' => 300.0,
                    'articleWidth' => 400.0,
                    'articleHeight' => 500.0,
                    'stockOutStrategy' => 'FIFO',
                    'standardLoadingEquipment' => 'KARTON',
                    'leQuantity' => 200.0,
                ],
                [
                    'articleId' => 1,
                    'articleNr' => '56789',
                    'articleName' => 'TestArtikel 2',
                    'articleCategory' => 'TestKategorie 2',
                    'articleWeight' => 1300.0,
                    'articleEan' => '123456789',
                    'articleUnit' => 'Stk',
                    'articleDepth' => 300.0,
                    'articleWidth' => 400.0,
                    'articleHeight' => 500.0,
                    'stockOutStrategy' => 'FIFO',
                    'standardLoadingEquipment' => 'KARTON',
                    'leQuantity' => 200.0,
                ],
            ]);

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $jsonResponse = $this->articleDataHandler->getArticle($articleNrInput);

        self::assertInstanceOf(JsonResponse::class, $jsonResponse);
    }

    public function testAddArticle(): void
    {
        $article = new Article();

        $dateTime = new DateTime('2023-06-06 12:00:00');
        $this->dateTimeService
            ->expects($this->once())
            ->method('createDateTime')
            ->willReturn($dateTime);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($article);
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->articleDataHandler->addArticle($article);

        self::assertEquals($dateTime, $article->getCreatedAt());
    }

    public function testUpdateArticle(): void
    {
        $article = new Article();

        $dateTime = new DateTime('2023-06-06 12:00:00');
        $this->dateTimeService
            ->expects($this->once())
            ->method('createDateTime')
            ->willReturn($dateTime);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($article);
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->articleDataHandler->updateArticle($article);

        self::assertEquals($dateTime, $article->getUpdatedAt());
    }

    public function testDeleteArticle(): void
    {
        $article = new Article();

        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($article);
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->articleDataHandler->deleteArticle($article);
    }

    public function testGetLastArticle(): void
    {
        $lastArticle = new Article();
        $lastArticle->setArticleId(123);

        $repository = $this->createMock(EntityRepository::class);

        $repository
            ->expects($this->once())
            ->method('findBy')
            ->with([], ['articleId' => 'DESC'], 1, 0)
            ->willReturn([$lastArticle]);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Article::class)
            ->willReturn($repository);

        $article = $this->articleDataHandler->getLastArticle();

        self::assertSame($lastArticle, $article);
    }
}
