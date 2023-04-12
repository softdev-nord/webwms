<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\ArticleRepository;

/**
 * @package:    WebWMS\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        Article
 */
#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ApiResource(
    extraProperties: [
        'standard_put' => true,
    ],
)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $articleId;

    #[ORM\Column(name: 'article_nr', type: 'string', length: 255, nullable: false)]
    private string $articleNr;

    #[ORM\Column(name: 'article_name', type: 'string', length: 255, nullable: false)]
    private string $articleName;

    #[ORM\Column(name: 'article_category', type: 'string', length: 255, nullable: false)]
    private string $articleCategory;

    #[ORM\Column(name: 'article_weight', type: 'decimal', precision: 10, scale: 2, nullable: false)]
    private float $articleWeight;

    #[ORM\Column(name: 'article_ean', type: 'string', length: 15, nullable: false)]
    private string $articleEan;

    #[ORM\Column(name: 'article_unit', type: 'string', length: 10, nullable: false)]
    private string $articleUnit;

    #[ORM\Column(name: 'article_depth', type: 'decimal', precision: 6, scale: 2, nullable: false)]
    private float $articleDepth;

    #[ORM\Column(name: 'article_width', type: 'decimal', precision: 6, scale: 2, nullable: false)]
    private float $articleWidth;

    #[ORM\Column(name: 'article_height', type: 'decimal', precision: 6, scale: 2, nullable: false)]
    private float $articleHeight;

    #[ORM\Column(name: 'stock_out_strategy', type: 'string', length: 10, nullable: false)]
    private string $stockOutStrategy;

    #[ORM\Column(name: 'standard_loading_equipment', type: 'string', length: 10, nullable: false)]
    private string $standardLoadingEquipment;

    #[ORM\Column(name: 'le_quantity', type: 'decimal', precision: 6, scale: 2, nullable: false)]
    private float $leQuantity;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt;

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

    public function getArticleCategory(): string
    {
        return $this->articleCategory;
    }

    public function setArticleCategory(string $articleCategory): self
    {
        $this->articleCategory = $articleCategory;

        return $this;
    }

    public function getArticleWeight(): float
    {
        return $this->articleWeight;
    }

    public function setArticleWeight(float $articleWeight): self
    {
        $this->articleWeight = $articleWeight;

        return $this;
    }

    public function getArticleEan(): string
    {
        return $this->articleEan;
    }

    public function setArticleEan(string $articleEan): self
    {
        $this->articleEan = $articleEan;

        return $this;
    }

    public function getArticleUnit(): string
    {
        return $this->articleUnit;
    }

    public function setArticleUnit(string $articleUnit): self
    {
        $this->articleUnit = $articleUnit;

        return $this;
    }

    public function getArticleDepth(): float
    {
        return $this->articleDepth;
    }

    public function setArticleDepth(float $articleDepth): self
    {
        $this->articleDepth = $articleDepth;

        return $this;
    }

    public function getArticleWidth(): float
    {
        return $this->articleWidth;
    }

    public function setArticleWidth(float $articleWidth): self
    {
        $this->articleWidth = $articleWidth;

        return $this;
    }

    public function getArticleHeight(): float
    {
        return $this->articleHeight;
    }

    public function setArticleHeight(float $articleHeight): self
    {
        $this->articleHeight = $articleHeight;

        return $this;
    }

    public function getStockOutStrategy(): string
    {
        return $this->stockOutStrategy;
    }

    public function setStockOutStrategy(string $stockOutStrategy): self
    {
        $this->stockOutStrategy = $stockOutStrategy;

        return $this;
    }

    public function getLeQuantity(): float
    {
        return $this->leQuantity;
    }

    public function setLeQuantity(float $leQuantity): self
    {
        $this->leQuantity = $leQuantity;

        return $this;
    }

    public function getStandardLoadingEquipment(): string
    {
        return $this->standardLoadingEquipment;
    }

    public function setStandardLoadingEquipment(string $standardLoadingEquipment): self
    {
        $this->standardLoadingEquipment = $standardLoadingEquipment;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
