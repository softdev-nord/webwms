<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\Article;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'ArticleTest'
)]
#[CoversClass(Article::class)]
final class ArticleTest extends TestCase
{
    private Article $article;

    private DateTime $dateTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article = new Article();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setArticleId() and getArticleId()
        $articleId = 1;
        $this->article->setArticleId($articleId);
        self::assertSame($articleId, $this->article->getArticleId());

        // Test setArticleNr() and getArticleNr()
        $articleNr = '12345';
        $this->article->setArticleNr($articleNr);
        self::assertSame($articleNr, $this->article->getArticleNr());

        // Test setArticleName() and getArticleName()
        $articleName = 'Test Article Name';
        $this->article->setArticleName($articleName);
        self::assertSame($articleName, $this->article->getArticleName());

        // Test setArticleCategory() and getArticleCategory()
        $articleCategory = 'Test Category';
        $this->article->setArticleCategory($articleCategory);
        self::assertSame($articleCategory, $this->article->getArticleCategory());

        // Test setArticleWeight() and getArticleWeight()
        $articleWeight = 2019.00;
        $this->article->setArticleWeight($articleWeight);
        self::assertSame($articleWeight, $this->article->getArticleWeight());

        // Test setArticleEan() and getArticleEan()
        $articleEan = '1234567890';
        $this->article->setArticleEan($articleEan);
        self::assertSame($articleEan, $this->article->getArticleEan());

        // Test setArticleUnit() and getArticleUnit()
        $articleUnit = 'Stk';
        $this->article->setArticleUnit($articleUnit);
        self::assertSame($articleUnit, $this->article->getArticleUnit());

        // Test setArticleDepth() and getArticleDepth()
        $articleDepth = 2019.00;
        $this->article->setArticleDepth($articleDepth);
        self::assertSame($articleDepth, $this->article->getArticleDepth());

        // Test setArticleWidth() and getArticleWidth()
        $articleWidth = 2019.00;
        $this->article->setArticleWidth($articleWidth);
        self::assertSame($articleWidth, $this->article->getArticleWidth());

        // Test setArticleHeight() and getArticleHeight()
        $articleHeight = 2019.00;
        $this->article->setArticleHeight($articleHeight);
        self::assertSame($articleHeight, $this->article->getArticleHeight());

        // Test setStockOutStrategy() and getStockOutStrategy()
        $stockOutStrategy = 'FIFO';
        $this->article->setStockOutStrategy($stockOutStrategy);
        self::assertSame($stockOutStrategy, $this->article->getStockOutStrategy());

        // Test setLeQuantity() and getLeQuantity()
        $leQuantity = 2019.00;
        $this->article->setLeQuantity($leQuantity);
        self::assertSame($leQuantity, $this->article->getLeQuantity());

        // Test setStandardLoadingEquipment() and getStandardLoadingEquipment()
        $standardLoadingEquipment = 'BLOCK';
        $this->article->setStandardLoadingEquipment($standardLoadingEquipment);
        self::assertSame($standardLoadingEquipment, $this->article->getStandardLoadingEquipment());

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
