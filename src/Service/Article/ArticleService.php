<?php

declare(strict_types=1);

namespace WebWMS\Service\Article;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\ArticleEntity;
use WebWMS\Service\DataHandlers\Article\ArticleDataHandler;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ArticleService
 */
class ArticleService
{
    public function __construct(
        private readonly ArticleDataHandler $articleDataHandler
    ) {
    }

    public function getArticleById(int $articleId): ?ArticleEntity
    {
        return $this->articleDataHandler->getArticleById($articleId);
    }

    public function getArticleByNr(string $articleNr): ?ArticleEntity
    {
        return $this->articleDataHandler->getArticleByNr($articleNr);
    }

    public function getAllArticles(): JsonResponse
    {
        return $this->articleDataHandler->getAllArticlesWithJoin();
    }

    public function getArticle(?string $articleNrInput): JsonResponse
    {
        return $this->articleDataHandler->getArticle($articleNrInput);
    }

    public function addArticle(ArticleEntity $articleEntity): void
    {
        $this->articleDataHandler->addArticle($articleEntity);
    }

    public function updateArticle(ArticleEntity $articleEntity): void
    {
        $this->articleDataHandler->updateArticle($articleEntity);
    }

    public function deleteArticle(ArticleEntity $articleEntity): void
    {
        $this->articleDataHandler->deleteArticle($articleEntity);
    }

    public function getLastArticle(): ArticleEntity
    {
        return $this->articleDataHandler->getLastArticle();
    }
}
