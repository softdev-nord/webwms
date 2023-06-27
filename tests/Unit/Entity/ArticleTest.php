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

    private \DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article = new Article();
        $this->dateTime = new \DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setArticleId() and getArticleId()
        $articleId = 1;
        $this->article->setArticleId($articleId);
        self::assertEquals($articleId, $this->article->getArticleId());

        // Test setArticleNr() and getArticleNr()
        $articleNr = '12345';
        $this->article->setArticleNr($articleNr);
        self::assertEquals($articleNr, $this->article->getArticleNr());

        // Test setArticleName() and getArticleName()
        $articleName = 'Test Article Name';
        $this->article->setArticleName($articleName);
        self::assertEquals($articleName, $this->article->getArticleName());

        // Test setArticleCategory() and getArticleCategory()
        $articleCategory = 'Test Category';
        $this->article->setArticleCategory($articleCategory);
        self::assertEquals($articleCategory, $this->article->getArticleCategory());

        // Test setArticleWeight() and getArticleWeight()
        $articleWeight = 2019.00;
        $this->article->setArticleWeight($articleWeight);
        self::assertEquals($articleWeight, $this->article->getArticleWeight());

        // Test setArticleEan() and getArticleEan()
        $articleEan = '1234567890';
        $this->article->setArticleEan($articleEan);
        self::assertEquals($articleEan, $this->article->getArticleEan());

        // Test setArticleUnit() and getArticleUnit()
        $articleUnit = 'Stk';
        $this->article->setArticleUnit($articleUnit);
        self::assertEquals($articleUnit, $this->article->getArticleUnit());

        // Test setArticleDepth() and getArticleDepth()
        $articleDepth = 2019.00;
        $this->article->setArticleDepth($articleDepth);
        self::assertEquals($articleDepth, $this->article->getArticleDepth());

        // Test setArticleWidth() and getArticleWidth()
        $articleWidth = 2019.00;
        $this->article->setArticleWidth($articleWidth);
        self::assertEquals($articleWidth, $this->article->getArticleWidth());

        // Test setArticleHeight() and getArticleHeight()
        $articleHeight = 2019.00;
        $this->article->setArticleHeight($articleHeight);
        self::assertEquals($articleHeight, $this->article->getArticleHeight());

        // Test setStockOutStrategy() and getStockOutStrategy()
        $stockOutStrategy = 'FIFO';
        $this->article->setStockOutStrategy($stockOutStrategy);
        self::assertEquals($stockOutStrategy, $this->article->getStockOutStrategy());

        // Test setLeQuantity() and getLeQuantity()
        $leQuantity = 2019.00;
        $this->article->setLeQuantity($leQuantity);
        self::assertEquals($leQuantity, $this->article->getLeQuantity());

        // Test setStandardLoadingEquipment() and getStandardLoadingEquipment()
        $standardLoadingEquipment = 'BLOCK';
        $this->article->setStandardLoadingEquipment($standardLoadingEquipment);
        self::assertEquals($standardLoadingEquipment, $this->article->getStandardLoadingEquipment());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->article->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->article->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->article->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->article->getUpdatedAt());
    }
}
