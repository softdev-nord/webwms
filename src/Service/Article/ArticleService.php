<?php

declare(strict_types=1);

namespace WebWMS\Service\Article;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
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

    public function getArticleApi(int $articleId): ?Article
    {
        $article = $this->articleDataHandler->getArticleById($articleId);

        if (!$article) {
            throw new EntityNotFoundException('Article with id '.$articleId.' does not exist!');
        }

        return $article;
    }

    public function getArticleById(int $articleId): ?Article
    {
        return $this->articleDataHandler->getArticleById($articleId);
    }

    public function getAllArticlesApi(): ?array
    {
        return $this->articleDataHandler->getAllArticles();
    }

    /**
     * @throws Exception
     */
    public function getAllArticles(): JsonResponse
    {
        return $this->articleDataHandler->getAllArticlesWithJoin();
    }

    public function addArticleApi(Request $request): Article
    {
        $requestData = $request->request->all();

        return $this->articleDataHandler->addArticle($requestData);
    }

    public function updateArticleApi(Request $request): ?Article
    {
        $requestData = $request->request->all();

        return $this->articleDataHandler->updateArticle($requestData);
    }

    public function deleteArticleApi(int $articleId): void
    {
        $article = $this->entityManager
            ->getRepository(Article::class)
            ->find($articleId);

        if ($article) {
            $this->articleDataHandler->delete($article);
        }
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

    /**
     * @throws \Exception
     */
    public function updateArticle($requestData): ?Article
    {
        $article = $this->articleDataHandler->getArticleById(
            (int) $requestData['articleId']
        );

        if (!$article) {
            return null;
        }

        $article->setArticleNr($requestData['articleNr']);
        $article->setArticleName($requestData['articleName']);
        $article->setArticleCategory($requestData['articleCategory']);
        $article->setArticleWeight((float) $requestData['articleWeight']);
        $article->setArticleEan($requestData['articleEan']);
        $article->setArticleUnit($requestData['articleUnit']);
        $article->setArticleDepth((float) $requestData['articleDepth']);
        $article->setArticleWidth((float) $requestData['articleWidth']);
        $article->setArticleHeight((float) $requestData['articleHeight']);
        $article->setStockOutStrategy($requestData['stockOutStrategy']);
        $article->setLeQuantity((float) $requestData['leQuantity']);
        $article->setStandardLoadingEquipment($requestData['standardLoadingEquipment']);
        $article->setUpdatedAt($this->dateTimeService->createDateTime());

        $this->articleDataHandler->update($article);

        return $article;
    }

    public function deleteArticle(int $articleNr): void
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
