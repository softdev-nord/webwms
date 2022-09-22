<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\ArticleRepository;

/**
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private mixed $articleId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private mixed $articleNr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private mixed $articleName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private mixed $articleCategory;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private mixed $articleWeight;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private mixed $articleEan;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private mixed $articleUnit;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private mixed $articleDepth;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private mixed $articleWidth;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private mixed $articleHeight;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private mixed $stockOutStrategy;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private mixed $standardLoadingEquipment;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private mixed $leQuantity;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $articleCreatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $articleUpdatedAt;

    public function getArticleId(): mixed
    {
        return $this->articleId;
    }

    public function setArticleId(mixed $articleId): void
    {
        $this->articleId = $articleId;
    }

    public function getArticleNr(): mixed
    {
        return $this->articleNr;
    }

    public function setArticleNr(mixed $articleNr): void
    {
        $this->articleNr = $articleNr;
    }

    public function getArticleName(): mixed
    {
        return $this->articleName;
    }

    public function setArticleName(mixed $articleName): void
    {
        $this->articleName = $articleName;
    }

    public function getArticleCategory(): mixed
    {
        return $this->articleCategory;
    }

    public function setArticleCategory(mixed $articleCategory): void
    {
        $this->articleCategory = $articleCategory;
    }

    public function getArticleWeight(): mixed
    {
        return $this->articleWeight;
    }

    public function setArticleWeight(mixed $articleWeight): void
    {
        $this->articleWeight = $articleWeight;
    }

    public function getArticleEan(): mixed
    {
        return $this->articleEan;
    }

    public function setArticleEan(mixed $articleEan): void
    {
        $this->articleEan = $articleEan;
    }

    public function getArticleUnit(): mixed
    {
        return $this->articleUnit;
    }

    public function setArticleUnit(mixed $articleUnit): void
    {
        $this->articleUnit = $articleUnit;
    }

    public function getArticleDepth(): mixed
    {
        return $this->articleDepth;
    }

    public function setArticleDepth(mixed $articleDepth): void
    {
        $this->articleDepth = $articleDepth;
    }

    public function getArticleWidth(): mixed
    {
        return $this->articleWidth;
    }

    public function setArticleWidth(mixed $articleWidth): void
    {
        $this->articleWidth = $articleWidth;
    }

    public function getArticleHeight(): mixed
    {
        return $this->articleHeight;
    }

    public function setArticleHeight(mixed $articleHeight): void
    {
        $this->articleHeight = $articleHeight;
    }

    public function getStockOutStrategy(): mixed
    {
        return $this->stockOutStrategy;
    }

    public function setStockOutStrategy(mixed $stockOutStrategy): void
    {
        $this->stockOutStrategy = $stockOutStrategy;
    }

    public function getLeQuantity(): mixed
    {
        return $this->leQuantity;
    }

    public function setLeQuantity(mixed $leQuantity): void
    {
        $this->leQuantity = $leQuantity;
    }

    public function getStandardLoadingEquipment(): mixed
    {
        return $this->standardLoadingEquipment;
    }

    public function setStandardLoadingEquipment(mixed $standardLoadingEquipment): void
    {
        $this->standardLoadingEquipment = $standardLoadingEquipment;
    }

    public function getArticleCreatedAt(): mixed
    {
        return $this->articleCreatedAt;
    }

    public function setArticleCreatedAt(mixed $articleCreatedAt): void
    {
        $this->articleCreatedAt = $articleCreatedAt;
    }

    public function getArticleUpdatedAt(): mixed
    {
        return $this->articleUpdatedAt;
    }

    public function setArticleUpdatedAt(mixed $articleUpdatedAt): void
    {
        $this->articleUpdatedAt = $articleUpdatedAt;
    }
}
