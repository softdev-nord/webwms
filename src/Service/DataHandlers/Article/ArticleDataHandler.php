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
            ->select('art.article_id, art.article_nr, art.article_name, art.article_category, art.article_weight, art.article_ean, art.article_unit, art.article_depth, art.article_width, art.article_height, art.created_at, art.updated_at')
            ->from('article', 'art');

        $results = $queryBuilder->executeQuery()->fetchAllAssociative();

        foreach ($results as $key => $result) {
            $results[$key]['in_stock'] = 0.00;
            $results[$key]['incoming_stock'] = 0.00;
            $results[$key]['reserved_stock'] = 0.00;

            /** @var int $articleNr */
            $articleNr = $result['article_nr'];

            $stockOccupancies = $this->stockOccupancyService->getStockOccupancyByArticleNr($articleNr);

            $inStock = array_column($stockOccupancies, 'in_stock');
            $incomingStock = array_column($stockOccupancies, 'incoming_stock');
            $reservedStock = array_column($stockOccupancies, 'reserved_stock');

            $results[$key]['in_stock'] += array_sum($inStock);
            $results[$key]['incoming_stock'] += array_sum($incomingStock);
            $results[$key]['reserved_stock'] += array_sum($reservedStock);
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
