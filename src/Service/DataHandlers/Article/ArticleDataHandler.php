<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Article;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Article;

/**
 * @package:    WebWMS\Service\DataHandlers
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        ArticleDataHandler
 */
class ArticleDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function save(Article $article): void
    {
        $this->entityManager->persist($article);
        $this->entityManager->flush();
    }

    public function update(Article $article): void
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
            ->find($articleId);
    }

    public function getArticleByNr(int $articleNr): ?Article
    {
        return $this->entityManager
            ->getRepository(Article::class)
            ->findOneBy(['article_nr' => $articleNr]);
    }

    /**
     * @throws Exception
     */
    public function getAllArticlesWithJoin(): JsonResponse
    {
        $conn = $this->entityManager->getConnection();

        $sql = "SELECT art.article_id, art.article_nr, art.article_name, art.article_category, art.article_weight, art.article_ean, art.article_unit, art.article_depth, art.article_width, art.article_height,
                (SELECT (SUM(IF(transport_history.tr_typ = '1', transport_history.tr_quantity, 0.000))) - (SUM(IF(transport_history.tr_typ = '2', transport_history.tr_quantity, 0.000))) 
                    FROM transport_history WHERE transport_history.article_nr = art.article_nr GROUP BY transport_history.article_nr LIMIT 1) AS lbw_menge
                FROM transport_history AS tph
                RIGHT OUTER JOIN article AS art
                    ON tph.article_nr = art.article_nr
                GROUP BY art.article_nr";

        $data = $conn->fetchAllAssociative($sql);

        return new JsonResponse($data);
    }
}
