<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Repository\ArticleRepository;

/**
 * @package:    WebWMS\Tests\Unit\Repository
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        ArticleRepositoryTest
 */
#[CoversClass(ArticleRepository::class)]
final class ArticleRepositoryTest extends TestCase
{
    public function testConstruct(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $articleRepository = new ArticleRepository($registry);

        self::assertInstanceOf(ArticleRepository::class, $articleRepository);
    }

    //
    //    public function testFind(): void
    //    {
    //        $article = new ArticleController();
    //        $entityManager = $this->createMock(EntityManagerInterface::class);
    //        $entityManager
    //            ->expects(self::once())
    //            ->method('find')
    //            ->with(ArticleController::class, 1)
    //            ->willReturn($article);
    //
    //        $registry = $this->createMock(ManagerRegistry::class);
    //        $registry
    //            ->expects(self::once())
    //            ->method('getManager')
    //            ->willReturn($entityManager);
    //
    //        $repository = new ArticleRepository($registry);
    //
    //        self::assertSame($article, $repository->find(1));
    //    }
    //
    //    public function testFindOneBy(): void
    //    {
    //        $article = new ArticleController();
    //        $entityManager = $this->createMock(EntityManagerInterface::class);
    //        $entityManager
    //            ->expects(self::once())
    //            ->method('getRepository')
    //            ->with(ArticleController::class)
    //            ->willReturn($repository = $this->createMock(ArticleRepository::class));
    //        $repository
    //            ->expects(self::once())
    //            ->method('findOneBy')
    //            ->with(['id' => 123])
    //            ->willReturn($article);
    //
    //        $registry = $this->createMock(ManagerRegistry::class);
    //        $registry
    //            ->expects(self::once())
    //            ->method('getManager')
    //            ->willReturn($entityManager);
    //
    //        $repository = new ArticleRepository($registry);
    //
    //        self::assertSame($article, $repository->findOneBy(['id' => 123]));
    //    }
    //
    //    public function testFindAll(): void
    //    {
    //        $articles = [new ArticleController(), new ArticleController()];
    //
    //        $entityManager = $this->createMock(EntityManagerInterface::class);
    //        $entityManager
    //            ->expects(self::once())
    //            ->method('getRepository')
    //            ->with(ArticleController::class)
    //            ->willReturn($repository = $this->createMock(ArticleRepository::class));
    //        $repository->expects(self::once())
    //            ->method('findAll')
    //            ->willReturn($articles);
    //
    //        $registry = $this->createMock(ManagerRegistry::class);
    //        $registry
    //            ->expects(self::once())
    //            ->method('getManager')
    //            ->willReturn($entityManager);
    //
    //        $repository = new ArticleRepository($registry);
    //
    //        self::assertSame($articles, $repository->findAll());
    //    }
    //
    //    public function testFindBy(): void
    //    {
    //        $articles = [new ArticleController(), new ArticleController()];
    //
    //        $entityManager = $this->createMock(EntityManagerInterface::class);
    //        $entityManager
    //            ->expects(self::once())
    //            ->method('getRepository')
    //            ->with(ArticleController::class)
    //            ->willReturn($repository = $this->createMock(ArticleRepository::class));
    //        $repository
    //            ->expects(self::once())
    //            ->method('findBy')
    //            ->with(['id' => 123], ['createdAt' => 'ASC'], 10, 0)
    //            ->willReturn($articles);
    //
    //        $registry = $this->createMock(ManagerRegistry::class);
    //        $registry
    //            ->expects(self::once())
    //            ->method('getManager')
    //            ->willReturn($entityManager);
    //
    //        $repository = new ArticleRepository($registry);
    //
    //        self::assertSame($articles, $repository->findBy(['id' => 123], ['createdAt' => 'ASC'], 10, 0));
    //    }
}
