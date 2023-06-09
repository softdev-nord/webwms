<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Article;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Article;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ArticleDataHandler
 */
class ArticleDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService
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
        return $this->entityManager
            ->getRepository(Article::class)
            ->findOneBy(['articleId' => $articleId]);
    }

    public function getArticleByNr(string $articleNr): ?Article
    {
        return $this->entityManager
            ->getRepository(Article::class)
            ->findOneBy(['articleNr' => $articleNr]);
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

    public function getAllArticlesWithJoin(): JsonResponse
    {
        $connection = $this->entityManager->getConnection();

        $sql = "SELECT art.article_id, art.article_nr, art.article_name, art.article_category, art.article_weight, art.article_ean, art.article_unit, art.article_depth, art.article_width, art.article_height, art.created_at, art.updated_at,
                (SELECT (SUM(IF(transport_history.tr_type = '1', transport_history.tr_quantity, 0.000))) - (SUM(IF(transport_history.tr_type = '2', transport_history.tr_quantity, 0.000)))

                FROM transport_history WHERE transport_history.article_nr = art.article_nr GROUP BY transport_history.article_nr LIMIT 1) AS lbw_menge
                FROM transport_history AS tph
                RIGHT OUTER JOIN article AS art
                    ON tph.article_nr = art.article_nr
                GROUP BY art.article_nr";

        $data = $connection->fetchAllAssociative($sql);

        return new JsonResponse($data);
    }

    public function getArticle(string|null $articleNrInput): JsonResponse
    {
        $data = [];
        if ($articleNrInput !== null) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder
                ->select('art')
                ->from(Article::class, 'art')
                ->where('art.articleNr LIKE :article_nr')
                ->setParameter(':article_nr', '' . $articleNrInput . '%');

            $articles = $queryBuilder->getQuery()->getArrayResult();

            foreach ($articles as $article) {
                $name = $article['articleId'] . '|' .
                    $article['articleNr'] . '|' .
                    $article['articleName'] . '|' .
                    $article['articleCategory'] . '|' .
                    $article['articleWeight'] . '|' .
                    $article['articleEan'] . '|' .
                    $article['articleUnit'] . '|' .
                    $article['articleDepth'] . '|' .
                    $article['articleWidth'] . '|' .
                    $article['articleHeight'] . '|' .
                    $article['stockOutStrategy'] . '|' .
                    $article['standardLoadingEquipment'] . '|' .
                    $article['leQuantity'];
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
        $lastArticle = $this->entityManager
            ->getRepository(Article::class)
            ->findBy([], ['articleId' => 'DESC'],1, 0);

        return $lastArticle[0];
    }
}
