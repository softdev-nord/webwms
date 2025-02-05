<?php

declare(strict_types=1);

namespace WebWMS\Service\Article;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Article;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DataHandlers\Article\ArticleDataHandler;

#[ClassInformation(
    package: 'WebWMS\Service',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'ArticleService'
)]
readonly class ArticleService
{
    public function __construct(
        private ArticleDataHandler $articleDataHandler,
    ) {
    }

    public function getArticleById(int $articleId): ?Article
    {
        return $this->articleDataHandler->getArticleById($articleId);
    }

    public function getArticleByNr(string $articleNr): ?Article
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

    public function addArticle(Article $article): void
    {
        $this->articleDataHandler->addArticle($article);
    }

    public function updateArticle(Article $article): void
    {
        $this->articleDataHandler->updateArticle($article);
    }

    public function deleteArticle(Article $article): void
    {
        $this->articleDataHandler->deleteArticle($article);
    }

    public function getLastArticle(): Article
    {
        return $this->articleDataHandler->getLastArticle();
    }
}
