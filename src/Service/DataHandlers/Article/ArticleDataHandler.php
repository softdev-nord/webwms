<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Article;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @throws Exception
     */
    public function getAllArticlesWithJoin(): JsonResponse
    {
        $conn = $this->entityManager->getConnection();

        $sql = "SELECT art.article_id, art.article_nr, art.article_name, art.article_category, art.article_weight, art.article_ean, art.article_unit, art.article_depth, art.article_width, art.article_height, art.updated_at,
                (SELECT (SUM(IF(transport_history.tr_type = '1', transport_history.tr_quantity, 0.000))) - (SUM(IF(transport_history.tr_type = '2', transport_history.tr_quantity, 0.000)))

                    FROM transport_history WHERE transport_history.article_nr = art.article_nr GROUP BY transport_history.article_nr LIMIT 1) AS lbw_menge
                FROM transport_history AS tph
                RIGHT OUTER JOIN article AS art
                    ON tph.article_nr = art.article_nr
                GROUP BY art.article_nr";

        $data = $conn->fetchAllAssociative($sql);

        return new JsonResponse($data);
    }

    /**
     * @param array<string|float> $requestData
     */
    public function addArticle(array $requestData): Article
    {
        $article = new Article();

        $article->setArticleNr((string) $requestData['articleNr']);
        $article->setArticleName((string) $requestData['articleName']);
        $article->setArticleCategory((string) $requestData['articleCategory']);
        $article->setArticleWeight((float) $requestData['articleWeight']);
        $article->setArticleEan((string) $requestData['articleEan']);
        $article->setArticleUnit((string) $requestData['articleUnit']);
        $article->setArticleDepth((float) $requestData['articleDepth']);
        $article->setArticleWidth((float) $requestData['articleWidth']);
        $article->setArticleHeight((float) $requestData['articleHeight']);
        $article->setStockOutStrategy((string) $requestData['stockOutStrategy']);
        $article->setLeQuantity((float) $requestData['leQuantity']);
        $article->setStandardLoadingEquipment((string) $requestData['standardLoadingEquipment']);
        $article->setCreatedAt($this->dateTimeService->createDateTime());
        $this->save($article);

        return $article;
    }

    public function updateArticle(Request $request): ?Article
    {
        $requestData = $request->request->all()['edit_article'];
        $article = $this->entityManager
            ->getRepository(Article::class)
            ->findOneBy(['articleNr' => $requestData['articleNr']]);

        if (!$article) {
            return null;
        }

        $article->setArticleNr($requestData['articleNr']);
        $article->setArticleName($requestData['articleName']);
        $article->setArticleCategory($requestData['articleCategory']);
        $article->setArticleWeight($requestData['articleWeight']);
        $article->setArticleEan($requestData['articleEan']);
        $article->setArticleUnit($requestData['articleUnit']);
        $article->setArticleDepth($requestData['articleDepth']);
        $article->setArticleWidth($requestData['articleWidth']);
        $article->setArticleHeight($requestData['articleHeight']);
        $article->setStockOutStrategy($requestData['stockOutStrategy']);
        $article->setLeQuantity($requestData['leQuantity']);
        $article->setStandardLoadingEquipment($requestData['standardLoadingEquipment']);
        $article->setCreatedAt($this->dateTimeService->createDateTime());
        $this->save($article);

        return $article;
    }

    public function deleteArticle(string $articleNr): void
    {
        $article = $this->getArticleByNr($articleNr);

        if (null !== $article) {
            $this->delete($article);
        }
    }
}
