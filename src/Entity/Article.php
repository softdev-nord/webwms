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

    /**
     * @return mixed
     */
    public function getArticleId(): mixed
    {
        return $this->articleId;
    }

    /**
     * @param mixed $articleId
     */
    public function setArticleId(mixed $articleId): void
    {
        $this->articleId = $articleId;
    }

    /**
     * @return mixed
     */
    public function getArticleNr(): mixed
    {
        return $this->articleNr;
    }

    /**
     * @param mixed $articleNr
     */
    public function setArticleNr(mixed $articleNr): void
    {
        $this->articleNr = $articleNr;
    }

    /**
     * @return mixed
     */
    public function getArticleName(): mixed
    {
        return $this->articleName;
    }

    /**
     * @param mixed $articleName
     */
    public function setArticleName(mixed $articleName): void
    {
        $this->articleName = $articleName;
    }

    /**
     * @return mixed
     */
    public function getArticleCategory(): mixed
    {
        return $this->articleCategory;
    }

    /**
     * @param mixed $articleCategory
     */
    public function setArticleCategory(mixed $articleCategory): void
    {
        $this->articleCategory = $articleCategory;
    }

    /**
     * @return mixed
     */
    public function getArticleWeight(): mixed
    {
        return $this->articleWeight;
    }

    /**
     * @param mixed $articleWeight
     */
    public function setArticleWeight(mixed $articleWeight): void
    {
        $this->articleWeight = $articleWeight;
    }

    /**
     * @return mixed
     */
    public function getArticleEan(): mixed
    {
        return $this->articleEan;
    }

    /**
     * @param mixed $articleEan
     */
    public function setArticleEan(mixed $articleEan): void
    {
        $this->articleEan = $articleEan;
    }

    /**
     * @return mixed
     */
    public function getArticleUnit(): mixed
    {
        return $this->articleUnit;
    }

    /**
     * @param mixed $articleUnit
     */
    public function setArticleUnit(mixed $articleUnit): void
    {
        $this->articleUnit = $articleUnit;
    }

    /**
     * @return mixed
     */
    public function getArticleDepth(): mixed
    {
        return $this->articleDepth;
    }

    /**
     * @param mixed $articleDepth
     */
    public function setArticleDepth(mixed $articleDepth): void
    {
        $this->articleDepth = $articleDepth;
    }

    /**
     * @return mixed
     */
    public function getArticleWidth(): mixed
    {
        return $this->articleWidth;
    }

    /**
     * @param mixed $articleWidth
     */
    public function setArticleWidth(mixed $articleWidth): void
    {
        $this->articleWidth = $articleWidth;
    }

    /**
     * @return mixed
     */
    public function getArticleHeight(): mixed
    {
        return $this->articleHeight;
    }

    /**
     * @param mixed $articleHeight
     */
    public function setArticleHeight(mixed $articleHeight): void
    {
        $this->articleHeight = $articleHeight;
    }

    /**
     * @return mixed
     */
    public function getStockOutStrategy(): mixed
    {
        return $this->stockOutStrategy;
    }

    /**
     * @param mixed $stockOutStrategy
     */
    public function setStockOutStrategy(mixed $stockOutStrategy): void
    {
        $this->stockOutStrategy = $stockOutStrategy;
    }

    /**
     * @return mixed
     */
    public function getLeQuantity(): mixed
    {
        return $this->leQuantity;
    }

    /**
     * @param mixed $leQuantity
     */
    public function setLeQuantity(mixed $leQuantity): void
    {
        $this->leQuantity = $leQuantity;
    }

    /**
     * @return mixed
     */
    public function getStandardLoadingEquipment(): mixed
    {
        return $this->standardLoadingEquipment;
    }

    /**
     * @param mixed $standardLoadingEquipment
     */
    public function setStandardLoadingEquipment(mixed $standardLoadingEquipment): void
    {
        $this->standardLoadingEquipment = $standardLoadingEquipment;
    }

    /**
     * @return mixed
     */
    public function getArticleCreatedAt(): mixed
    {
        return $this->articleCreatedAt;
    }

    /**
     * @param mixed $articleCreatedAt
     */
    public function setArticleCreatedAt(mixed $articleCreatedAt): void
    {
        $this->articleCreatedAt = $articleCreatedAt;
    }

    /**
     * @return mixed
     */
    public function getArticleUpdatedAt(): mixed
    {
        return $this->articleUpdatedAt;
    }

    /**
     * @param mixed $articleUpdatedAt
     */
    public function setArticleUpdatedAt(mixed $articleUpdatedAt): void
    {
        $this->articleUpdatedAt = $articleUpdatedAt;
    }
}
