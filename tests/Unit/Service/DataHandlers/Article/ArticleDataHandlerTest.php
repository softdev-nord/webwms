<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\DataHandlers\Article;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\ArticleEntity;
use WebWMS\Service\DataHandlers\Article\ArticleDataHandler;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Tests\Unit\Service\DataHandlers\ArticleController
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ArticleDataHandlerTest
 */
#[CoversClass(ArticleDataHandler::class)]
final class ArticleDataHandlerTest extends TestCase
{
    private ArticleDataHandler $articleDataHandler;

    private MockObject $entityManager;

    private MockObject $dateTimeService;

    #[Override]
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->dateTimeService = $this->createMock(DateTimeService::class);

        $this->articleDataHandler = new ArticleDataHandler(
            $this->entityManager,
            $this->dateTimeService
        );
    }

    public function testSave(): void
    {
        $article = $this->createMock(ArticleEntity::class);

        $this->entityManager
            ->expects(self::once())
            ->method('persist')
            ->with($article);
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->articleDataHandler->save($article);
    }

    public function testDelete(): void
    {
        $article = $this->createMock(ArticleEntity::class);

        $this->entityManager
            ->expects(self::once())
            ->method('remove')
            ->with($article);
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->articleDataHandler->delete($article);
    }

    public function testGetArticleById(): void
    {
        $articleId = 123;
        $expectedArticle = $this->createMock(ArticleEntity::class);
        $repository = $this->createMock(EntityRepository::class);

        $repository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['articleId' => $articleId])
            ->willReturn($expectedArticle);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->with(ArticleEntity::class)
            ->willReturn($repository);

        $article = $this->articleDataHandler->getArticleById($articleId);

        self::assertSame($expectedArticle, $article);
    }

    public function testGetArticleByNr(): void
    {
        $articleNr = 'ABC123';
        $expectedArticle = $this->createMock(ArticleEntity::class);
        $repository = $this->createMock(EntityRepository::class);

        $repository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['articleNr' => $articleNr])
            ->willReturn($expectedArticle);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->with(ArticleEntity::class)
            ->willReturn($repository);

        $article = $this->articleDataHandler->getArticleByNr($articleNr);

        self::assertSame($expectedArticle, $article);
    }

    public function testGetAllArticles(): void
    {
        $expectedArticles = [$this->createMock(ArticleEntity::class)];
        $repository = $this->createMock(EntityRepository::class);

        $repository
            ->expects(self::once())
            ->method('findAll')
            ->willReturn($expectedArticles);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->with(ArticleEntity::class)
            ->willReturn($repository);

        $articles = $this->articleDataHandler->getAllArticles();

        self::assertSame($expectedArticles, $articles);
    }

    public function testGetAllArticlesWithJoin(): void
    {
        $expectedData = [['article_id' => 1, 'article_nr' => 'ABC123']];
        $connection = $this->createMock(Connection::class);

        $this->entityManager
            ->expects(self::once())
            ->method('getConnection')
            ->willReturn($connection);

        $connection
            ->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(self::isType('string'))
            ->willReturn($expectedData);

        $response = $this->articleDataHandler->getAllArticlesWithJoin();

        self::assertInstanceOf(JsonResponse::class, $response);
    }

    public function testGetArticle(): void
    {
        $articleNrInput = '12345';

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(AbstractQuery::class);

        $queryBuilder
            ->expects(self::once())
            ->method('select')
            ->willReturnSelf();
        $queryBuilder
            ->expects(self::once())
            ->method('from')
            ->willReturnSelf();
        $queryBuilder
            ->expects(self::once())
            ->method('where')
            ->willReturnSelf();
        $queryBuilder
            ->expects(self::once())
            ->method('setParameter')
            ->willReturnSelf();
        $queryBuilder
            ->expects(self::once())
            ->method('getQuery')
            ->willReturn($query);

        $query
            ->expects(self::once())
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
            ->expects(self::once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);

        $jsonResponse = $this->articleDataHandler->getArticle($articleNrInput);

        self::assertInstanceOf(JsonResponse::class, $jsonResponse);
    }

    public function testAddArticle(): void
    {
        $articleEntity = new ArticleEntity();

        $dateTime = new DateTime('2023-06-06 12:00:00');
        $this->dateTimeService
            ->expects(self::once())
            ->method('createDateTime')
            ->willReturn($dateTime);

        $this->entityManager
            ->expects(self::once())
            ->method('persist')
            ->with($articleEntity);
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->articleDataHandler->addArticle($articleEntity);

        self::assertEquals($dateTime, $articleEntity->getCreatedAt());
    }

    public function testUpdateArticle(): void
    {
        $articleEntity = new ArticleEntity();

        $dateTime = new DateTime('2023-06-06 12:00:00');
        $this->dateTimeService
            ->expects(self::once())
            ->method('createDateTime')
            ->willReturn($dateTime);

        $this->entityManager
            ->expects(self::once())
            ->method('persist')
            ->with($articleEntity);
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->articleDataHandler->updateArticle($articleEntity);

        self::assertEquals($dateTime, $articleEntity->getUpdatedAt());
    }

    public function testDeleteArticle(): void
    {
        $articleEntity = new ArticleEntity();

        $this->entityManager
            ->expects(self::once())
            ->method('remove')
            ->with($articleEntity);
        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->articleDataHandler->deleteArticle($articleEntity);
    }

    public function testGetLastArticle(): void
    {
        $articleEntity = new ArticleEntity();
        $articleEntity->setArticleId(123);

        $repository = $this->createMock(EntityRepository::class);

        $repository
            ->expects(self::once())
            ->method('findBy')
            ->with([], ['articleId' => 'DESC'], 1, 0)
            ->willReturn([$articleEntity]);

        $this->entityManager
            ->expects(self::once())
            ->method('getRepository')
            ->with(ArticleEntity::class)
            ->willReturn($repository);

        $article = $this->articleDataHandler->getLastArticle();

        self::assertSame($articleEntity, $article);
    }
}
