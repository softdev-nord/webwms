<?php

declare(strict_types=1);
//
// declare(strict_types=1);
//
// namespace WebWMS\Tests\Functional\Service\DataHandlers\Article;
//
// use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
// use WebWMS\Entity\Article;
//
// /**
// * @package:    WebWMS\Tests\Unit\Entity
// * @author:     SoftDev Nord, Rene Irrgang
// * @copyright:  Copyright © 2019-2023, SoftDev Nord
// * Class        ArticleDataHandlerTest.
// *
// * @covers \WebWMS\Service\DataHandlers\Article\ArticleDataHandler
// */
// final class ArticleDataHandlerTest extends KernelTestCase
// {
//    private EntityManagerInterface $entityManager;
//
//    public function setUp(): void
//    {
//        parent::setUp();
//
//        $kernel = self::bootKernel();
//
//        $this->entityManager = $kernel->getContainer()
//            ->get('doctrine')
//            ->getManager();
//    }
//
//    protected function tearDown(): void
//    {
//        parent::tearDown();
//
//        $this->entityManager->close();
//    }
//
//    public function testGetArticleById(): void
//    {
//        $articleId = 1;
//        $article = $this->entityManager
//            ->getRepository(Article::class)
//            ->findOneBy(['articleId' => $articleId]);
//
//        if ($article !== null) {
//            $this->assertEquals($articleId, $article->getArticleId());
//        }
//    }
//
//    public function testGetArticleByNr(): void
//    {
//        $articleNr = '60004';
//        $article = $this->entityManager
//            ->getRepository(Article::class)
//            ->findOneBy(['articleNr' => $articleNr]);
//
//        if ($article === null) {
//            return;
//        }
//
//        $this->assertEquals($articleNr, $article->getArticleNr());
//    }
//
//    public function testGetAllArticles(): void
//    {
//        $articles = $this->entityManager
//            ->getRepository(Article::class)
//            ->findAll();
//
//        $this->assertNotEmpty($articles);
//    }
// }
