<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WebWMS\Entity\Article;
use WebWMS\Repository\ArticleRepository;
use WebWMS\Service\DataHandlers\ArticleDataHandler;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        ArticleService
 */
class ArticleService
{
    public function __construct(
        private ManagerRegistry $doctrine,
        private ArticleRepository $articleRepository,
        private ArticleDataHandler $articleDataHandler
    )
    {
    }

    public function getArticleRepository(): ArticleRepository
    {
        return $this->articleRepository;
    }

    public function getArticleApi(int $articleId): ?Article
    {
        $article = $this->articleRepository->findById($articleId);

        if (!$article) {
            throw new EntityNotFoundException('Article with id '.$articleId.' does not exist!');
        }

        return $article;
    }

    public function getAllArticlesApi(): ?array
    {
        return $this->articleRepository->findAll();
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
        float $articleHeight
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
        float $articleHeight
    ): ?Article {
        $article = $this->articleRepository->findById($articleId);
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
        $this->articleDataHandler->save($article);

        return $article;
    }

    public function deleteArticleApi(int $articleId): void
    {
        $article = $this->articleRepository->findById($articleId);
        if ($article) {
            $this->articleRepository->delete($article);
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
     */
    public function getArticle(): JsonResponse
    {
        $connection = $this->doctrine->getConnection();

        $numOfBoxArt = !empty($_GET['numOfBoxArt']) ? $_GET['numOfBoxArt'] : '';
        $name = !empty($_GET['article_nr']) ? strtolower(trim($_GET['article_nr'])) : '';

        $boxName = 'article_nr';

        switch ($numOfBoxArt) {
            case 1:
                $boxName = 'article_name';
                break;
            case 2:
                $boxName = 'id';
                break;
            case 3:
                $boxName = 'art_ean';
                break;
            case 4:
                $boxName = 'art_kat';
                break;
        }

        $data = [];
        if (!empty($_GET['name_art'])) {
            $name = strtolower(trim($_GET['name_art']));

            $sqlArt = "SELECT article_nr, article_name, article_id, article_ean, article_category FROM article where LOWER($boxName) LIKE '".$name."%'";
            $stmt = $connection->executeQuery($sqlArt);

            while ($row = $stmt->fetchAssociative()) {
                $name = $row['article_nr'].'|'.$row['article_name'].'|'.$row['id'].'|'.$row['article_ean'].'|'.$row['article_category'];
                $data[] = $name;
            }
        }

        return new JsonResponse($data);
    }

    public function addArticle(Request $request)
    {
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

        $this->articleDataHandler->save($article);
    }

    public function updateArticle($requestData)
    {
        $article = $this->articleRepository->findById((int) $requestData['article_id']);
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

        $this->articleDataHandler->update($article);

        return $article;
    }

    /**
     * Get last article.
     */
    public function getLastArticle(): ?Article
    {
        $articleRepository = $this->getArticleRepository();

        return $articleRepository->findOneBy([], ['article_nr' => 'DESC']);
    }

    protected function createNotFoundException(string $message = 'Not Found', \Throwable $previous = null): NotFoundHttpException
    {
        return new NotFoundHttpException($message, $previous);
    }
}
