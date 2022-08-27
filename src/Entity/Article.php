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
    private mixed $article_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private mixed $article_nr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private mixed $article_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private mixed $article_category;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private mixed $article_weight;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private mixed $article_ean;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private mixed $article_unit;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private mixed $article_depth;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private mixed $article_width;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private mixed $article_height;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private mixed $stock_out_strategy;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private mixed $standard_loading_equipment;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private mixed $le_quantity;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $article_created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $article_updated_at;

    /**
     * @return mixed
     */
    public function getArticleId(): mixed
    {
        return $this->article_id;
    }

    /**
     * @param mixed $article_id
     */
    public function setArticleId(mixed $article_id): void
    {
        $this->article_id = $article_id;
    }

    /**
     * @return mixed
     */
    public function getArticleNr(): mixed
    {
        return $this->article_nr;
    }

    /**
     * @param mixed $article_nr
     */
    public function setArticleNr(mixed $article_nr): void
    {
        $this->article_nr = $article_nr;
    }

    /**
     * @return mixed
     */
    public function getArticleName(): mixed
    {
        return $this->article_name;
    }

    /**
     * @param mixed $article_name
     */
    public function setArticleName(mixed $article_name): void
    {
        $this->article_name = $article_name;
    }

    /**
     * @return mixed
     */
    public function getArticleCategory(): mixed
    {
        return $this->article_category;
    }

    /**
     * @param mixed $article_category
     */
    public function setArticleCategory(mixed $article_category): void
    {
        $this->article_category = $article_category;
    }

    /**
     * @return mixed
     */
    public function getArticleWeight(): mixed
    {
        return $this->article_weight;
    }

    /**
     * @param mixed $article_weight
     */
    public function setArticleWeight(mixed $article_weight): void
    {
        $this->article_weight = $article_weight;
    }

    /**
     * @return mixed
     */
    public function getArticleEan(): mixed
    {
        return $this->article_ean;
    }

    /**
     * @param mixed $article_ean
     */
    public function setArticleEan(mixed $article_ean): void
    {
        $this->article_ean = $article_ean;
    }

    /**
     * @return mixed
     */
    public function getArticleUnit(): mixed
    {
        return $this->article_unit;
    }

    /**
     * @param mixed $article_unit
     */
    public function setArticleUnit(mixed $article_unit): void
    {
        $this->article_unit = $article_unit;
    }

    /**
     * @return mixed
     */
    public function getArticleDepth(): mixed
    {
        return $this->article_depth;
    }

    /**
     * @param mixed $article_depth
     */
    public function setArticleDepth(mixed $article_depth): void
    {
        $this->article_depth = $article_depth;
    }

    /**
     * @return mixed
     */
    public function getArticleWidth(): mixed
    {
        return $this->article_width;
    }

    /**
     * @param mixed $article_width
     */
    public function setArticleWidth(mixed $article_width): void
    {
        $this->article_width = $article_width;
    }

    /**
     * @return mixed
     */
    public function getArticleHeight(): mixed
    {
        return $this->article_height;
    }

    /**
     * @param mixed $article_height
     */
    public function setArticleHeight(mixed $article_height): void
    {
        $this->article_height = $article_height;
    }

    /**
     * @return mixed
     */
    public function getStockOutStrategy(): mixed
    {
        return $this->stock_out_strategy;
    }

    /**
     * @param mixed $stock_out_strategy
     */
    public function setStockOutStrategy(mixed $stock_out_strategy): void
    {
        $this->stock_out_strategy = $stock_out_strategy;
    }

    /**
     * @return mixed
     */
    public function getLeQuantity(): mixed
    {
        return $this->le_quantity;
    }

    /**
     * @param mixed $le_quantity
     */
    public function setLeQuantity(mixed $le_quantity): void
    {
        $this->le_quantity = $le_quantity;
    }

    /**
     * @return mixed
     */
    public function getStandardLoadingEquipment(): mixed
    {
        return $this->standard_loading_equipment;
    }

    /**
     * @param mixed $standard_loading_equipment
     */
    public function setStandardLoadingEquipment(mixed $standard_loading_equipment): void
    {
        $this->standard_loading_equipment = $standard_loading_equipment;
    }

    /**
     * @return mixed
     */
    public function getArticleCreatedAt(): mixed
    {
        return $this->article_created_at;
    }

    /**
     * @param mixed $article_created_at
     */
    public function setArticleCreatedAt(mixed $article_created_at): void
    {
        $this->article_created_at = $article_created_at;
    }

    /**
     * @return mixed
     */
    public function getArticleUpdatedAt(): mixed
    {
        return $this->article_updated_at;
    }

    /**
     * @param mixed $article_updated_at
     */
    public function setArticleUpdatedAt(mixed $article_updated_at): void
    {
        $this->article_updated_at = $article_updated_at;
    }
}
