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
    private $article_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $article_nr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $article_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $article_category;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $article_weight;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $article_ean;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $article_unit;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private $article_depth;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private $article_width;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private $article_height;

    /**
     * @return mixed
     */
    public function getArticleId()
    {
        return $this->article_id;
    }

    /**
     * @param mixed $article_id
     */
    public function setArticleId($article_id): void
    {
        $this->article_id = $article_id;
    }

    /**
     * @return mixed
     */
    public function getArticleNr()
    {
        return $this->article_nr;
    }

    /**
     * @param mixed $article_nr
     */
    public function setArticleNr($article_nr): void
    {
        $this->article_nr = $article_nr;
    }

    /**
     * @return mixed
     */
    public function getArticleName()
    {
        return $this->article_name;
    }

    /**
     * @param mixed $article_name
     */
    public function setArticleName($article_name): void
    {
        $this->article_name = $article_name;
    }

    /**
     * @return mixed
     */
    public function getArticleCategory()
    {
        return $this->article_category;
    }

    /**
     * @param mixed $article_category
     */
    public function setArticleCategory($article_category): void
    {
        $this->article_category = $article_category;
    }

    /**
     * @return mixed
     */
    public function getArticleWeight()
    {
        return $this->article_weight;
    }

    /**
     * @param mixed $article_weight
     */
    public function setArticleWeight($article_weight): void
    {
        $this->article_weight = $article_weight;
    }

    /**
     * @return mixed
     */
    public function getArticleEan()
    {
        return $this->article_ean;
    }

    /**
     * @param mixed $article_ean
     */
    public function setArticleEan($article_ean): void
    {
        $this->article_ean = $article_ean;
    }

    /**
     * @return mixed
     */
    public function getArticleUnit()
    {
        return $this->article_unit;
    }

    /**
     * @param mixed $article_unit
     */
    public function setArticleUnit($article_unit): void
    {
        $this->article_unit = $article_unit;
    }

    /**
     * @return mixed
     */
    public function getArticleDepth()
    {
        return $this->article_depth;
    }

    /**
     * @param mixed $article_depth
     */
    public function setArticleDepth($article_depth): void
    {
        $this->article_depth = $article_depth;
    }

    /**
     * @return mixed
     */
    public function getArticleWidth()
    {
        return $this->article_width;
    }

    /**
     * @param mixed $article_width
     */
    public function setArticleWidth($article_width): void
    {
        $this->article_width = $article_width;
    }

    /**
     * @return mixed
     */
    public function getArticleHeight()
    {
        return $this->article_height;
    }

    /**
     * @param mixed $article_height
     */
    public function setArticleHeight($article_height): void
    {
        $this->article_height = $article_height;
    }
}
