<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Article;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\ArticleEntity;
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
        private readonly EntityManagerInterface $entityManager,
        private readonly DateTimeService $dateTimeService
    ) {
    }

    public function save(ArticleEntity $articleEntity): void
    {
        $this->entityManager->persist($articleEntity);
        $this->entityManager->flush();
    }

    public function delete(ArticleEntity $articleEntity): void
    {
        $this->entityManager->remove($articleEntity);
        $this->entityManager->flush();
    }

    public function getArticleById(int $articleId): ?ArticleEntity
    {
        return $this->entityManager
            ->getRepository(ArticleEntity::class)
            ->findOneBy(['articleId' => $articleId]);
    }

    public function getArticleByNr(string $articleNr): ?ArticleEntity
    {
        return $this->entityManager
            ->getRepository(ArticleEntity::class)
            ->findOneBy(['articleNr' => $articleNr]);
    }

    /**
     * @return object[]|null
     */
    public function getAllArticles(): ?array
    {
        return $this->entityManager
            ->getRepository(ArticleEntity::class)
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
                ->from(ArticleEntity::class, 'art')
                ->where('art.articleNr LIKE :article_nr')
                ->setParameter(':article_nr', '%' . $articleNrInput . '%');

            $articles = $queryBuilder->getQuery()->getArrayResult();

            foreach ($articles as $article) {
                $name = $article['articleId'] . ' | ' .
                    $article['articleNr'] . ' | ' .
                    $article['articleName'] . ' | ' .
                    $article['articleCategory'] . ' | ' .
                    $article['articleWeight'] . ' | ' .
                    $article['articleEan'] . ' | ' .
                    $article['articleUnit'] . ' | ' .
                    $article['articleDepth'] . ' | ' .
                    $article['articleWidth'] . ' | ' .
                    $article['articleHeight'] . ' | ' .
                    $article['stockOutStrategy'] . ' | ' .
                    $article['standardLoadingEquipment'] . ' | ' .
                    $article['leQuantity'];
                $data[] = $name;
            }
        }

        // dd($data);
        return new JsonResponse($data);
    }

    public function addArticle(ArticleEntity $articleEntity): void
    {
        $articleEntity->setCreatedAt($this->dateTimeService->createDateTime());

        $this->save($articleEntity);
    }

    public function updateArticle(ArticleEntity $articleEntity): void
    {
        $articleEntity->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->save($articleEntity);
    }

    public function deleteArticle(ArticleEntity $articleEntity): void
    {
        $this->delete($articleEntity);
    }

    public function getLastArticle(): ArticleEntity
    {
        $lastArticle = $this->entityManager
            ->getRepository(ArticleEntity::class)
            ->findBy([], ['articleId' => 'DESC'], 1, 0);

        return $lastArticle[0];
    }
}
