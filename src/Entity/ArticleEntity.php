<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use WebWMS\Repository\ArticleRepository;

/**
 * @package:    WebWMS\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ArticleEntity
 */
#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'skip_null_values' => false,
                'groups' => ['article:read'],
            ]
        ),
        new GetCollection(
            normalizationContext: [
                'skip_null_values' => false,
                'groups' => ['article:read'],
            ]
        ),
        new Post(
            denormalizationContext: [
                'groups' => ['article:write'],
            ]
        ),
        new Put(
            denormalizationContext: [
                'groups' => ['article:write'],
            ]
        ),
        new Patch(
            denormalizationContext: [
                'groups' => ['article:write'],
            ]
        ),
        new Delete(
            denormalizationContext: [
                'groups' => ['article:write'],
            ]
        ),
    ],
    formats: ['json'],
    normalizationContext: ['groups' => ['article:read']],
    denormalizationContext: ['groups' => ['article:write']]
)]
class ArticleEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['article:read', 'article:write'])]
    private int $articleId;

    #[ORM\Column(name: 'article_nr', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['article:read', 'article:write'])]
    private string $articleNr;

    #[ORM\Column(name: 'article_name', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['article:read', 'article:write'])]
    private string $articleName;

    #[ORM\Column(name: 'article_category', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['article:read', 'article:write'])]
    private string $articleCategory;

    #[ORM\Column(name: 'article_weight', type: Types::DECIMAL, precision: 10, scale: 2, nullable: false)]
    #[Groups(['article:read', 'article:write'])]
    private float $articleWeight;

    #[ORM\Column(name: 'article_ean', type: Types::STRING, length: 15, nullable: false)]
    #[Groups(['article:read', 'article:write'])]
    private string $articleEan;

    #[ORM\Column(name: 'article_unit', type: Types::STRING, length: 10, nullable: false)]
    #[Groups(['article:read', 'article:write'])]
    private string $articleUnit;

    #[ORM\Column(name: 'article_depth', type: Types::DECIMAL, precision: 6, scale: 2, nullable: false)]
    #[Groups(['article:read', 'article:write'])]
    private float $articleDepth;

    #[ORM\Column(name: 'article_width', type: Types::DECIMAL, precision: 6, scale: 2, nullable: false)]
    #[Groups(['article:read', 'article:write'])]
    private float $articleWidth;

    #[ORM\Column(name: 'article_height', type: Types::DECIMAL, precision: 6, scale: 2, nullable: false)]
    #[Groups(['article:read', 'article:write'])]
    private float $articleHeight;

    #[ORM\Column(name: 'stock_out_strategy', type: Types::STRING, length: 10, nullable: false)]
    #[Groups(['article:read', 'article:write'])]
    private string $stockOutStrategy;

    #[ORM\Column(name: 'standard_loading_equipment', type: Types::STRING, length: 10, nullable: false)]
    #[Groups(['article:read', 'article:write'])]
    private string $standardLoadingEquipment;

    #[ORM\Column(name: 'le_quantity', type: Types::DECIMAL, precision: 6, scale: 2, nullable: false)]
    #[Groups(['article:read', 'article:write'])]
    private float $leQuantity;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['article:read', 'article:write'])]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['article:read', 'article:write'])]
    private ?DateTimeInterface $updatedAt = null;

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

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
