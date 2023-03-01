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
 * @copyright:  Copyright © 2022, SoftDev Nord
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

    /**
     * @return Article|null Returns an array of Article objects
     */
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
        $conn = $this->entityManager->getConnection();

        $sql = "SELECT art.article_id, art.article_nr, art.article_name, art.article_category, art.article_weight, art.article_ean, art.article_unit, art.article_depth, art.article_width, art.article_height, art.created_at, art.updated_at,
                (SELECT (SUM(IF(transport_history.tr_type = '1', transport_history.tr_quantity, 0.000))) - (SUM(IF(transport_history.tr_type = '2', transport_history.tr_quantity, 0.000)))

                    FROM transport_history WHERE transport_history.article_nr = art.article_nr GROUP BY transport_history.article_nr LIMIT 1) AS lbw_menge
                FROM transport_history AS tph
                RIGHT OUTER JOIN article AS art
                    ON tph.article_nr = art.article_nr
                GROUP BY art.article_nr";

        $data = $conn->fetchAllAssociative($sql);

        return new JsonResponse($data);
    }

    public function getArticle(): JsonResponse
    {
        $connection = $this->entityManager->getConnection();

        $numOfBoxArt = !empty(filter_input(INPUT_GET, 'numOfBoxArt')) ? filter_input(INPUT_GET, 'numOfBoxArt') : '';
        $name = !empty(filter_input(INPUT_GET, 'article_nr')) ? strtolower(trim(filter_input(INPUT_GET, 'article_nr'))) : '';

        $boxName = match ($numOfBoxArt) {
            'article_id' => 'article_id',
            'article_name' => 'article_name',
            default => 'article_nr',
        };

        $data = [];
        if (!empty(filter_input(INPUT_GET, 'name_art'))) {
            $name = strtolower(trim(filter_input(INPUT_GET, 'name_art')));

            $sqlArt = "SELECT * FROM article where LOWER($boxName) LIKE '" . $name . "%'";

            $stmt = $connection->executeQuery($sqlArt);

            while ($row = $stmt->fetchAssociative()) {
                $name = $row['article_id']
                    . '|' . $row['article_nr']
                    . '|' . $row['article_name']
                    . '|' . $row['article_category']
                    . '|' . $row['article_weight']
                    . '|' . $row['article_ean']
                    . '|' . $row['article_unit']
                    . '|' . $row['article_depth']
                    . '|' . $row['article_width']
                    . '|' . $row['article_height']
                    . '|' . $row['stock_out_strategy']
                    . '|' . $row['le_quantity']
                    . '|' . $row['standard_loading_equipment']
                    . '|' . $row['created_at']
                    . '|' . $row['updated_at'];
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
}
