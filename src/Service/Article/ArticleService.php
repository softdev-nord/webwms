<?php

declare(strict_types=1);

namespace WebWMS\Service\Article;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\Article;
use WebWMS\Service\DataHandlers\Article\ArticleDataHandler;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        ArticleService
 */
class ArticleService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ArticleDataHandler $articleDataHandler,
        private DateTimeService $dateTimeService
    ) {
    }

    public function getArticleById(int $articleId): ?Article
    {
        return $this->articleDataHandler->getArticleById($articleId);
    }

    /**
     * @throws Exception
     */
    public function getAllArticles(): JsonResponse
    {
        return $this->articleDataHandler->getAllArticlesWithJoin();
    }

    /**
     * Get article for.
     *
     * @throws Exception
     */
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

            $sqlArt = "SELECT * FROM article where LOWER($boxName) LIKE '".$name."%'";

            $stmt = $connection->executeQuery($sqlArt);

            while ($row = $stmt->fetchAssociative()) {
                $name = $row['article_id']
                    .'|'.$row['article_nr']
                    .'|'.$row['article_name']
                    .'|'.$row['article_category']
                    .'|'.$row['article_weight']
                    .'|'.$row['article_ean']
                    .'|'.$row['article_unit']
                    .'|'.$row['article_depth']
                    .'|'.$row['article_width']
                    .'|'.$row['article_height']
                    .'|'.$row['stock_out_strategy']
                    .'|'.$row['le_quantity']
                    .'|'.$row['standard_loading_equipment']
                    .'|'.$row['created_at']
                    .'|'.$row['updated_at'];
                $data[] = $name;
            }
        }

        return new JsonResponse($data);
    }

    /**
     * @throws \Exception
     */
    public function addArticle(Request $request): void
    {
        $params = $request->request->all()['add_article'];
        $article = new Article();

        $article->setArticleNr($params['articleNr']);
        $article->setArticleName($params['articleName']);
        $article->setArticleCategory($params['articleCategory']);
        $article->setArticleWeight((float) $params['articleWeight']);
        $article->setArticleEan($params['articleEan']);
        $article->setArticleUnit($params['articleUnit']);
        $article->setArticleDepth((float) $params['articleDepth']);
        $article->setArticleWidth((float) $params['articleWidth']);
        $article->setArticleHeight((float) $params['articleHeight']);
        $article->setStockOutStrategy($params['stockOutStrategy']);
        $article->setLeQuantity((float) $params['leQuantity']);
        $article->setStandardLoadingEquipment($params['standardLoadingEquipment']);
        $article->setCreatedAt($this->dateTimeService->createDateTime());

        $this->articleDataHandler->save($article);
    }

    public function updateArticle(Request $request): ?Article
    {
        return $this->articleDataHandler->updateArticle($request);
    }

    public function deleteArticle(string $articleNr): void
    {
        $this->articleDataHandler->deleteArticle($articleNr);
    }

    /**
     * Get last article.
     */
    public function getLastArticle(): ?Article
    {
        return $this->entityManager
            ->getRepository(Article::class)
            ->findOneBy([], ['articleNr' => 'DESC']);
    }
}
