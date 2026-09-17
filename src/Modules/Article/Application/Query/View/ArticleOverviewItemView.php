<?php

declare(strict_types=1);

namespace WebWMS\Modules\Article\Application\Query\View;

class ArticleOverviewItemView
{
    private int $articleId;
    private string $articleNr;
    private string $articleName;
    private string $articleDescription = '';
    private float $totalStock = 0.0;
    private float $incomingStock = 0.0;
    private int $locationCount = 0;

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function setArticleId(int $articleId): self
    {
        $this->articleId = $articleId;
        return $this;
    }

    public function getArticleNr(): string
    {
        return $this->articleNr;
    }

    public function setArticleNr(string $articleNr): self
    {
        $this->articleNr = $articleNr;
        return $this;
    }

    public function getArticleName(): string
    {
        return $this->articleName;
    }

    public function setArticleName(string $articleName): self
    {
        $this->articleName = $articleName;
        return $this;
    }

    public function getArticleDescription(): string
    {
        return $this->articleDescription;
    }

    public function setArticleDescription(string $articleDescription): self
    {
        $this->articleDescription = $articleDescription;
        return $this;
    }

    public function getTotalStock(): float
    {
        return $this->totalStock;
    }

    public function setTotalStock(float $totalStock): self
    {
        $this->totalStock = $totalStock;
        return $this;
    }

    public function getIncomingStock(): float
    {
        return $this->incomingStock;
    }

    public function setIncomingStock(float $incomingStock): self
    {
        $this->incomingStock = $incomingStock;
        return $this;
    }

    public function getLocationCount(): int
    {
        return $this->locationCount;
    }

    public function setLocationCount(int $locationCount): self
    {
        $this->locationCount = $locationCount;
        return $this;
    }
}

