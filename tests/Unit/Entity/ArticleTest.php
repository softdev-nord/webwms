<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\Article;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ArticleTest
 *
 * @covers \WebWMS\Entity\Article
 */
final class ArticleTest extends TestCase
{
    private Article $article;

    private \DateTimeImmutable $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article = new Article();
        $this->dateTime = new \DateTimeImmutable();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->article);
        unset($this->dateTime);
    }

    public function testGetArticleId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleId');
        $property->setValue($this->article, $expected);
        $this->assertSame($expected, $this->article->getArticleId());
    }

    public function testSetArticleId(): void
    {
        $expected = 2019;
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleId');
        $this->article->setArticleId($expected);
        $this->assertSame($expected, $property->getValue($this->article));
    }

    public function testGetArticleNr(): void
    {
        $expected = 'articleNr';
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleNr');
        $property->setValue($this->article, $expected);
        $this->assertSame($expected, $this->article->getArticleNr());
    }

    public function testSetArticleNr(): void
    {
        $expected = 'articleNr';
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleNr');
        $this->article->setArticleNr($expected);
        $this->assertSame($expected, $property->getValue($this->article));
    }

    public function testGetArticleName(): void
    {
        $expected = 'articleName';
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleName');
        $property->setValue($this->article, $expected);
        $this->assertSame($expected, $this->article->getArticleName());
    }

    public function testSetArticleName(): void
    {
        $expected = 'articleName';
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleName');
        $this->article->setArticleName($expected);
        $this->assertSame($expected, $property->getValue($this->article));
    }

    public function testGetArticleCategory(): void
    {
        $expected = 'articleCategory';
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleCategory');
        $property->setValue($this->article, $expected);
        $this->assertSame($expected, $this->article->getArticleCategory());
    }

    public function testSetArticleCategory(): void
    {
        $expected = 'articleCategory';
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleCategory');
        $this->article->setArticleCategory($expected);
        $this->assertSame($expected, $property->getValue($this->article));
    }

    public function testGetArticleWeight(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleWeight');
        $property->setValue($this->article, $expected);
        $this->assertSame($expected, $this->article->getArticleWeight());
    }

    public function testSetArticleWeight(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleWeight');
        $this->article->setArticleWeight($expected);
        $this->assertSame($expected, $property->getValue($this->article));
    }

    public function testGetArticleEan(): void
    {
        $expected = 'articleEan';
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleEan');
        $property->setValue($this->article, $expected);
        $this->assertSame($expected, $this->article->getArticleEan());
    }

    public function testSetArticleEan(): void
    {
        $expected = 'articleEan';
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleEan');
        $this->article->setArticleEan($expected);
        $this->assertSame($expected, $property->getValue($this->article));
    }

    public function testGetArticleUnit(): void
    {
        $expected = 'articleUnit';
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleUnit');
        $property->setValue($this->article, $expected);
        $this->assertSame($expected, $this->article->getArticleUnit());
    }

    public function testSetArticleUnit(): void
    {
        $expected = 'articleUnit';
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleUnit');
        $this->article->setArticleUnit($expected);
        $this->assertSame($expected, $property->getValue($this->article));
    }

    public function testGetArticleDepth(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleDepth');
        $property->setValue($this->article, $expected);
        $this->assertSame($expected, $this->article->getArticleDepth());
    }

    public function testSetArticleDepth(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleDepth');
        $this->article->setArticleDepth($expected);
        $this->assertSame($expected, $property->getValue($this->article));
    }

    public function testGetArticleWidth(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleWidth');
        $property->setValue($this->article, $expected);
        $this->assertSame($expected, $this->article->getArticleWidth());
    }

    public function testSetArticleWidth(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleWidth');
        $this->article->setArticleWidth($expected);
        $this->assertSame($expected, $property->getValue($this->article));
    }

    public function testGetArticleHeight(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleHeight');
        $property->setValue($this->article, $expected);
        $this->assertSame($expected, $this->article->getArticleHeight());
    }

    public function testSetArticleHeight(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('articleHeight');
        $this->article->setArticleHeight($expected);
        $this->assertSame($expected, $property->getValue($this->article));
    }

    public function testGetStockOutStrategy(): void
    {
        $expected = 'stockOutStrategy';
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('stockOutStrategy');
        $property->setValue($this->article, $expected);
        $this->assertSame($expected, $this->article->getStockOutStrategy());
    }

    public function testSetStockOutStrategy(): void
    {
        $expected = 'stockOutStrategy';
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('stockOutStrategy');
        $this->article->setStockOutStrategy($expected);
        $this->assertSame($expected, $property->getValue($this->article));
    }

    public function testGetLeQuantity(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('leQuantity');
        $property->setValue($this->article, $expected);
        $this->assertSame($expected, $this->article->getLeQuantity());
    }

    public function testSetLeQuantity(): void
    {
        $expected = 2019.00;
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('leQuantity');
        $this->article->setLeQuantity($expected);
        $this->assertSame($expected, $property->getValue($this->article));
    }

    public function testGetStandardLoadingEquipment(): void
    {
        $expected = 'standardLoadingEquipment';
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('standardLoadingEquipment');
        $property->setValue($this->article, $expected);
        $this->assertSame($expected, $this->article->getStandardLoadingEquipment());
    }

    public function testSetStandardLoadingEquipment(): void
    {
        $expected = 'standardLoadingEquipment';
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('standardLoadingEquipment');
        $this->article->setStandardLoadingEquipment($expected);
        $this->assertSame($expected, $property->getValue($this->article));
    }

    public function testGetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('createdAt');
        $property->setValue($this->article, $expected);
        $this->assertSame($expected, $this->article->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('createdAt');
        $this->article->setCreatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->article));
    }

    public function testGetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('updatedAt');
        $property->setValue($this->article, $expected);
        $this->assertSame($expected, $this->article->getUpdatedAt());
    }

    public function testSetUpdatedAt(): void
    {
        $expected = $this->dateTime;
        $property = (new \ReflectionClass(Article::class))
            ->getProperty('updatedAt');
        $this->article->setUpdatedAt($expected);
        $this->assertSame($expected, $property->getValue($this->article));
    }
}
