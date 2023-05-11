<?php

declare(strict_types=1);

namespace WebWMS\Tests\Functional\Service\DataHandlers\Article;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Article;
use WebWMS\Repository\ArticleRepository;
use WebWMS\Service\DataHandlers\Article\ArticleDataHandler;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ArticleDataHandlerTest.
 *
 * @covers \WebWMS\Service\DataHandlers\Article\ArticleDataHandler
 */
final class ArticleDataHandlerTest extends KernelTestCase
{
    /**
     * @var (EntityManagerInterface&MockObject)|MockObject
     */
    private MockObject|EntityManagerInterface $entityManagerMock;

    /**
     * @var (MockObject&DateTimeService)|MockObject
     */
    private MockObject|DateTimeService $dateTimeServiceMock;

    private ArticleDataHandler $articleDataHandler;

    public function setUp(): void
    {
        parent::setUp();
        $this->entityManagerMock = $this->getMockBuilder(EntityManagerInterface::class)
            ->getMock();

        $this->dateTimeServiceMock = $this->getMockBuilder(DateTimeService::class)
            ->getMock();

        $this->articleDataHandler = new ArticleDataHandler(
            $this->entityManagerMock,
            $this->dateTimeServiceMock,
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->dateTimeServiceMock);

    }

    public function testSave(): void
    {
        $article = new Article();

        $this->entityManagerMock
            ->expects(self::exactly(1))
            ->method('persist')
            ->with($article);
        $this->entityManagerMock
            ->expects(self::exactly(1))
            ->method('flush');

        $this->articleDataHandler->save($article);
    }

    public function testDelete(): void
    {
        $article = new Article();

        $this->entityManagerMock
            ->expects(self::exactly(1))
            ->method('remove')
            ->with($article);
        $this->entityManagerMock
            ->expects(self::exactly(1))
            ->method('flush');

        $this->articleDataHandler->delete($article);
    }

    public function testGetArticleById(): void
    {
        $article = new Article();
        $articleId = 1;
        $article->setArticleId($articleId);

        $repositoryMock = self::createMock(ArticleRepository::class);
        $repositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['articleId' => $articleId])
            ->willReturn($article);

        $this->entityManagerMock->expects(self::once())
            ->method('getRepository')
            ->with(Article::class)
            ->willReturn($repositoryMock);

        $result = $this->articleDataHandler->getArticleById($articleId);

        self::assertSame($article, $result);
    }

    public function testGetArticleByNr(): void
    {
        $article = new Article();
        $articleNr = '60004';
        $article->setArticleNr($articleNr);

        $repositoryMock = $this->createMock(ArticleRepository::class);
        $repositoryMock->expects(self::once())
            ->method('findOneBy')
            ->with(['articleNr' => $articleNr])
            ->willReturn($article);

        $this->entityManagerMock->expects(self::once())
            ->method('getRepository')
            ->with(Article::class)
            ->willReturn($repositoryMock);

        $result = $this->articleDataHandler->getArticleByNr($articleNr);

        self::assertSame($article, $result);
    }

    public function testGetAllArticles(): void
    {
        $articles = [
            new Article(),
            new Article(),
            new Article(),
        ];

        $repositoryMock = self::createMock(ArticleRepository::class);
        $repositoryMock->expects(self::once())
            ->method('findAll')
            ->willReturn($articles);

        $this->entityManagerMock->expects(self::once())
            ->method('getRepository')
            ->with(Article::class)
            ->willReturn($repositoryMock);

        $result = $this->articleDataHandler->getAllArticles();

        self::assertSame($articles, $result);
    }

    public function testGetAllArticlesWithJoin(): void
    {
        $mockConnection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->entityManagerMock->method('getConnection')
            ->willReturn($mockConnection);
        $mockConnection->method('fetchAllAssociative')
            ->willReturn([[
                'article_id' => 1,
                'article_nr' => '12345',
                'article_name' => 'Test Article',
                'article_category' => 'TestCategory',
                'article_weight' => 10.5,
                'article_ean' => '123456789',
                'article_unit' => 'PCS',
                'article_depth' => 5.5,
                'article_width' => 3.2,
                'article_height' => 6.8,
                'created_at' => '2021-07-13 12:00:00',
                'updated_at' => '2021-07-13 13:00:00',
                'lbw_menge' => 5,
            ]]);

        $response = $this->articleDataHandler->getAllArticlesWithJoin();
        $responseData = json_decode((string) $response->getContent(), true);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertEquals([[
            'article_id' => 1,
            'article_nr' => '12345',
            'article_name' => 'Test Article',
            'article_category' => 'TestCategory',
            'article_weight' => 10.5,
            'article_ean' => '123456789',
            'article_unit' => 'PCS',
            'article_depth' => 5.5,
            'article_width' => 3.2,
            'article_height' => 6.8,
            'created_at' => '2021-07-13 12:00:00',
            'updated_at' => '2021-07-13 13:00:00',
            'lbw_menge' => 5]], $responseData
        );
    }

    public function testGetArticle(): void
    {

        $_GET['numOfBoxArt'] = 'article_nr';
        $_GET['name_art'] = 'Test Article';

        $mockStmt = $this->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockStmt->method('fetchAssociative')
            ->willReturn(
                [
                    'article_id' => '1',
                    'article_nr' => '12345',
                    'article_name' => 'Test Article',
                    'article_category' => 'TestCategory',
                    'article_weight' => '10.5',
                    'article_ean' => '123456789',
                    'article_unit' => 'PCS',
                    'article_depth' => '5.5',
                    'article_width' => '3.2',
                    'article_height' => '6.8',
                    'stock_out_strategy' => 'FIFO',
                    'le_quantity' => '480',
                    'standard_loading_equipment' => 'BLOCK',
                    'created_at' => '2022-01-01 00:00:00',
                    'updated_at' => '2022-01-01 00:00:00',
                ]
            );

        $mockConnection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockConnection->method('executeQuery')
            ->will(self::returnValue($mockStmt));
        $this->entityManagerMock->method('getConnection')
            ->will(self::returnValue($mockConnection));
        $response = $this->articleDataHandler->getArticle();
        self::assertInstanceOf(JsonResponse::class, $response);
    }

    public function testGetArticleWithInvalidInput(): void
    {
        $_GET['numOfBoxArt'] = '';
        $_GET['name_art'] = 'apple';

        $output = $this->articleDataHandler->getArticle();
        self::assertInstanceOf(JsonResponse::class, $output);
        self::assertEquals([], json_decode((string) $output->getContent(), true));
    }
    public function testAddArticle(): void
    {
        $createdAt = new \DateTime();
        $this->dateTimeServiceMock
            ->expects(self::once())
            ->method('createDateTime')
            ->willReturn($createdAt);

        $articleMock = $this->getMockBuilder(Article::class)
            ->getMock();
        $articleMock
            ->expects(self::once())
            ->method('setCreatedAt')
            ->with($createdAt);

        $this->entityManagerMock
            ->expects(self::once())
            ->method('persist')
            ->with($articleMock);
        $this->entityManagerMock
            ->expects(self::once())
            ->method('flush');

        $this->articleDataHandler->addArticle($articleMock);
    }

    public function testUpdateArticle(): void
    {
        $updatedAt = new \DateTime();
        $this->dateTimeServiceMock
            ->expects(self::once())
            ->method('createDateTime')
            ->willReturn($updatedAt);

        $articleMock = $this->getMockBuilder(Article::class)
            ->getMock();
        $articleMock
            ->expects(self::once())
            ->method('setUpdatedAt')
            ->with($updatedAt);

        $this->entityManagerMock
            ->expects(self::once())
            ->method('persist')
            ->with($articleMock);
        $this->entityManagerMock
            ->expects(self::once())
            ->method('flush');

        $this->articleDataHandler->updateArticle($articleMock);
    }

    public function testDeleteArticle(): void
    {
        $articleMock = $this->getMockBuilder(Article::class)
            ->getMock();

        $this->entityManagerMock
            ->expects(self::once())
            ->method('remove')
            ->with($articleMock);
        $this->entityManagerMock
            ->expects(self::once())
            ->method('flush');

        $this->articleDataHandler->deleteArticle($articleMock);
    }
}
