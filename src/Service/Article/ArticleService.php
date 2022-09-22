<?php

declare(strict_types=1);

namespace WebWMS\Service\Article;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WebWMS\Entity\Article;
use WebWMS\Service\DataHandlers\Article\ArticleDataHandler;
use WebWMS\Service\TransportRequestService;

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
        private TransportRequestService $transportRequestService
    ) {
    }

    public function getArticleByNr(int $articleNr): ?Article
    {
        return $this->articleDataHandler->getArticleByNr($articleNr);
    }

    public function getArticleApi(int $articleId): ?Article
    {
        $article = $this->entityManager
            ->getRepository(Article::class)
            ->find($articleId);

        if (!$article) {
            throw new EntityNotFoundException('Article with id '.$articleId.' does not exist!');
        }

        return $article;
    }

    public function getAllArticlesApi(): ?array
    {
        return $this->entityManager
            ->getRepository(Article::class)
            ->findAll();
    }

    public function addArticleApi(
        int $articleNr,
        string $articleName,
        string $articleCategory,
        float $articleWeight,
        string $articleEan,
        string $articleUnit,
        float $articleDepth,
        float $articleWidth,
        float $articleHeight,
        string $stockOutStrategy,
        float $leQuantity,
        string $standardLoadingEquipment
    ): Article {
        $article = new Article();
        $article->setArticleNr($articleNr);
        $article->setArticleName($articleName);
        $article->setArticleCategory($articleCategory);
        $article->setArticleWeight($articleWeight);
        $article->setArticleEan($articleEan);
        $article->setArticleUnit($articleUnit);
        $article->setArticleDepth($articleDepth);
        $article->setArticleWidth($articleWidth);
        $article->setArticleHeight($articleHeight);
        $article->setStockOutStrategy($stockOutStrategy);
        $article->setLeQuantity($leQuantity);
        $article->setStandardLoadingEquipment($standardLoadingEquipment);
        $this->articleDataHandler->save($article);

        return $article;
    }

    public function updateArticleApi(
        int $articleId,
        int $articleNr,
        string $articleName,
        string $articleCategory,
        float $articleWeight,
        string $articleEan,
        string $articleUnit,
        float $articleDepth,
        float $articleWidth,
        float $articleHeight,
        string $stockOutStrategy,
        float $leQuantity,
        string $standardLoadingEquipment
    ): ?Article {
        $article = $this->entityManager
            ->getRepository(Article::class)
            ->find($articleId);

        if (!$article) {
            return null;
        }
        $article->setArticleNr($articleNr);
        $article->setArticleName($articleName);
        $article->setArticleCategory($articleCategory);
        $article->setArticleWeight($articleWeight);
        $article->setArticleEan($articleEan);
        $article->setArticleUnit($articleUnit);
        $article->setArticleDepth($articleDepth);
        $article->setArticleWidth($articleWidth);
        $article->setArticleHeight($articleHeight);
        $article->setStockOutStrategy($stockOutStrategy);
        $article->setLeQuantity($leQuantity);
        $article->setStandardLoadingEquipment($standardLoadingEquipment);
        $this->articleDataHandler->save($article);

        return $article;
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
     * @throws Exception
     */
    public function getAllArticles(): JsonResponse
    {
        $articles = $this->articleDataHandler->getAllArticlesWithJoin();

        if (!$articles) {
            throw $this->createNotFoundException('Keine Artikel gefunden');
        }

        return $articles;
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

        if (empty($boxName)) {
            $boxName = 'article_nr';
        }

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
                    .'|'.$row['article_created_at']
                    .'|'.$row['article_updated_at'];
                $data[] = $name;
            }
        }

        return new JsonResponse($data);
    }

    public function addArticle(Request $request)
    {
        $createdAt = new \DateTime('NOW', new \DateTimeZone('Europe/Berlin'));
        $params = $request->request->all()['add_new_article'];
        $lastArticle = $this->getLastArticle();
        $article = new Article();

        $article->setArticleNr($lastArticle->getArticleNr());
        $article->setArticleName($params['article_name']);
        $article->setArticleCategory($params['article_category']);
        $article->setArticleWeight($params['article_weight']);
        $article->setArticleEan($params['article_ean']);
        $article->setArticleUnit($params['article_unit']);
        $article->setArticleDepth($params['article_depth']);
        $article->setArticleWidth($params['article_width']);
        $article->setArticleHeight($params['article_height']);
        $article->setStockOutStrategy($params['stock_out_strategy']);
        $article->setLeQuantity($params['le_quantity']);
        $article->setStandardLoadingEquipment($params['standard_loading_equipment']);
        $article->setArticleCreatedAt($createdAt);

        $this->articleDataHandler->save($article);
    }

    public function updateArticle($requestData)
    {
        $updatedAt = new \DateTime('NOW', new \DateTimeZone('Europe/Berlin'));

        $article = $this->entityManager
            ->getRepository(Article::class)
            ->find((int) $requestData['article_id']);

        if (!$article) {
            return null;
        }

        $article->setArticleNr($requestData['article_nr']);
        $article->setArticleName($requestData['article_name']);
        $article->setArticleCategory($requestData['article_category']);
        $article->setArticleWeight($requestData['article_weight']);
        $article->setArticleEan($requestData['article_ean']);
        $article->setArticleUnit($requestData['article_unit']);
        $article->setArticleDepth($requestData['article_depth']);
        $article->setArticleWidth($requestData['article_width']);
        $article->setArticleHeight($requestData['article_height']);
        $article->setStockOutStrategy($requestData['stock_out_strategy']);
        $article->setLeQuantity($requestData['le_quantity']);
        $article->setStandardLoadingEquipment($requestData['standard_loading_equipment']);
        $article->setArticleUpdatedAt($updatedAt);

        $this->articleDataHandler->update($article);

        return $article;
    }

    /**
     * Get last article.
     */
    public function getLastArticle(): ?Article
    {
        return $this->entityManager
            ->getRepository(Article::class)
            ->findOneBy([], ['article_nr' => 'DESC']);
    }

    protected function createNotFoundException(string $message = 'Not Found', \Throwable $previous = null): NotFoundHttpException
    {
        return new NotFoundHttpException($message, $previous);
    }
}
