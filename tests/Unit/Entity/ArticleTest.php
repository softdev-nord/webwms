<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use DateTime;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\ArticleEntity;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        ArticleTest
 */
#[CoversClass(ArticleEntity::class)]
final class ArticleTest extends TestCase
{
    private ArticleEntity $articleEntity;

    private DateTime $dateTime;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->articleEntity = new ArticleEntity();
        $this->dateTime = new DateTime();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setArticleId() and getArticleId()
        $articleId = 1;
        $this->articleEntity->setArticleId($articleId);
        self::assertEquals($articleId, $this->articleEntity->getArticleId());

        // Test setArticleNr() and getArticleNr()
        $articleNr = '12345';
        $this->articleEntity->setArticleNr($articleNr);
        self::assertEquals($articleNr, $this->articleEntity->getArticleNr());

        // Test setArticleName() and getArticleName()
        $articleName = 'Test ArticleController Name';
        $this->articleEntity->setArticleName($articleName);
        self::assertEquals($articleName, $this->articleEntity->getArticleName());

        // Test setArticleCategory() and getArticleCategory()
        $articleCategory = 'Test Category';
        $this->articleEntity->setArticleCategory($articleCategory);
        self::assertEquals($articleCategory, $this->articleEntity->getArticleCategory());

        // Test setArticleWeight() and getArticleWeight()
        $articleWeight = '2019.00';
        $this->articleEntity->setArticleWeight($articleWeight);
        self::assertEquals($articleWeight, $this->articleEntity->getArticleWeight());

        // Test setArticleEan() and getArticleEan()
        $articleEan = '1234567890';
        $this->articleEntity->setArticleEan($articleEan);
        self::assertEquals($articleEan, $this->articleEntity->getArticleEan());

        // Test setArticleUnit() and getArticleUnit()
        $articleUnit = 'Stk';
        $this->articleEntity->setArticleUnit($articleUnit);
        self::assertEquals($articleUnit, $this->articleEntity->getArticleUnit());

        // Test setArticleDepth() and getArticleDepth()
        $articleDepth = '2019.00';
        $this->articleEntity->setArticleDepth($articleDepth);
        self::assertEquals($articleDepth, $this->articleEntity->getArticleDepth());

        // Test setArticleWidth() and getArticleWidth()
        $articleWidth = '2019.00';
        $this->articleEntity->setArticleWidth($articleWidth);
        self::assertEquals($articleWidth, $this->articleEntity->getArticleWidth());

        // Test setArticleHeight() and getArticleHeight()
        $articleHeight = '2019.00';
        $this->articleEntity->setArticleHeight($articleHeight);
        self::assertEquals($articleHeight, $this->articleEntity->getArticleHeight());

        // Test setStockOutStrategy() and getStockOutStrategy()
        $stockOutStrategy = 'FIFO';
        $this->articleEntity->setStockOutStrategy($stockOutStrategy);
        self::assertEquals($stockOutStrategy, $this->articleEntity->getStockOutStrategy());

        // Test setLeQuantity() and getLeQuantity()
        $leQuantity = '2019.00';
        $this->articleEntity->setLeQuantity($leQuantity);
        self::assertEquals($leQuantity, $this->articleEntity->getLeQuantity());

        // Test setStandardLoadingEquipment() and getStandardLoadingEquipment()
        $standardLoadingEquipment = 'BLOCK';
        $this->articleEntity->setStandardLoadingEquipment($standardLoadingEquipment);
        self::assertEquals($standardLoadingEquipment, $this->articleEntity->getStandardLoadingEquipment());

        // Test setCreatedAt() and getCreatedAt()
        $createdAt = $this->dateTime;
        $this->articleEntity->setCreatedAt($createdAt);
        self::assertEquals($createdAt, $this->articleEntity->getCreatedAt());

        // Test setUpdatedAt() and getUpdatedAt()
        $updatedAt = $this->dateTime;
        $this->articleEntity->setUpdatedAt($updatedAt);
        self::assertEquals($updatedAt, $this->articleEntity->getUpdatedAt());
    }
}
