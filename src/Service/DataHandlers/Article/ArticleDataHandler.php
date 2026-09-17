<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Article;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Article;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DateTimeService;
use WebWMS\Service\Stock\StockOccupancyService;

#[ClassInformation(
    package: 'WebWMS\Service\DataHandlers',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'ArticleDataHandler'
)]
readonly class ArticleDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private StockOccupancyService $stockOccupancyService,
        private DateTimeService $dateTimeService,
    ) {
    }

    public function save(Article $article): void
    {
        $this->entityManager->persist($article);
        $this->entityManager->flush();
    }

    public function delete(Article $article): void
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();
    }

    public function getArticleById(int $articleId): ?Article
    {
        /** @var Article|null $article */
        $article = $this->entityManager
            ->getRepository(Article::class)
            ->findOneBy(['articleId' => $articleId]);

        return $article;
    }

    public function getArticleByNr(string $articleNr): ?Article
    {
        /** @var Article|null $article */
        $article = $this->entityManager
            ->getRepository(Article::class)
            ->findOneBy(['articleNr' => $articleNr]);

        return $article;
    }

    /**
     * @return object[]|null
     */
    public function getAllArticles(): ?array
    {
        return $this->entityManager
            ->getRepository(Article::class)
            ->findAll();
    }

    /**
     * @throws Exception
     */
    public function getAllArticlesWithJoin(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();
        $queryBuilder
            ->select(
                '
                art.article_id as articleId,
                art.article_nr as articleNr,
                art.article_name as articleName,
                art.article_category as articleCategory,
                art.article_weight as articleWeight,
                art.article_ean as articleEan,
                art.article_unit as articleUnit,
                art.article_depth as articleDepth,
                art.article_width as articleWidth,
                art.article_height as articleHeight,
                art.created_at as createdAt,
                art.updated_at as updatedAt
                ')
            ->from('article', 'art');

        $results = $queryBuilder->executeQuery()->fetchAllAssociative();

        foreach ($results as $key => $result) {
            $results[$key]['inStock'] = 0.00;
            $results[$key]['incomingStock'] = 0.00;
            $results[$key]['reservedStock'] = 0.00;

            $articleId = (int)$result['articleId'];

            $stockOccupancies = $this->stockOccupancyService->getStockOccupancyByArticleId($articleId);

            $inStock = array_column($stockOccupancies, 'inStock');
            $incomingStock = array_column($stockOccupancies, 'incomingStock');
            $reservedStock = array_column($stockOccupancies, 'reservedStock');

            $results[$key]['inStock'] += array_sum($inStock);
            $results[$key]['incomingStock'] += array_sum($incomingStock);
            $results[$key]['reservedStock'] += array_sum($reservedStock);
        }

        return new JsonResponse($results);
    }

    public function getArticle(?string $articleNrInput): JsonResponse
    {
        $data = [];
        if ($articleNrInput !== null) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder
                ->select('art')
                ->from(Article::class, 'art')
                ->where('art.articleNr LIKE :article_nr')
                ->setParameter(':article_nr', '%' . $articleNrInput . '%');

            $articles = $queryBuilder->getQuery()->getArrayResult();

            foreach ($articles as $article) {
                $name = $article['articleId'] . ' | '
                    . $article['articleNr'] . ' | '
                    . $article['articleName'] . ' | '
                    . $article['articleCategory'] . ' | '
                    . $article['articleWeight'] . ' | '
                    . $article['articleEan'] . ' | '
                    . $article['articleUnit'] . ' | '
                    . $article['articleDepth'] . ' | '
                    . $article['articleWidth'] . ' | '
                    . $article['articleHeight'] . ' | '
                    . $article['stockOutStrategy'] . ' | '
                    . $article['standardLoadingEquipment'] . ' | '
                    . $article['leQuantity'];
                $data[] = $name;
            }
        }

        return new JsonResponse($data);
    }

    public function addArticle(Article $article): void
    {
        $article->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($article);
    }

    public function updateArticle(Article $article): void
    {
        $article->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($article);
    }

    public function deleteArticle(Article $article): void
    {
        $this->delete($article);
    }

    public function getLastArticle(): Article
    {
        /** @var Article[] $lastArticle */
        $lastArticle = $this->entityManager
            ->getRepository(Article::class)
            ->findBy([], ['articleId' => 'DESC'], 1, 0);

        return $lastArticle[0];
    }
}
