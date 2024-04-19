<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Validation;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\ArticleEntity;
use WebWMS\Service\Validation\ArticleValidationService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ArticleValidationServiceTest
 */
#[CoversClass(ArticleValidationService::class)]
final class ArticleValidationServiceTest extends TestCase
{
    private ArticleValidationService $articleValidationService;

    #[Override]
    protected function setUp(): void
    {
        $this->articleValidationService = new ArticleValidationService();
    }

    public function testValidateArticleDataReturnsErrorWhenRequiredFieldsAreEmpty(): void
    {
        $articleEntity = (new ArticleEntity())
            ->setArticleNr('')
            ->setArticleName('')
            ->setArticleCategory('')
            ->setArticleWeight(0.00)
            ->setArticleEan('')
            ->setArticleUnit('')
            ->setArticleDepth(0.00)
            ->setArticleWidth(0.00)
            ->setArticleHeight(0.00)
            ->setStockOutStrategy('')
            ->setStandardLoadingEquipment('');

        $responseData = $this->articleValidationService->validateArticleData($articleEntity);

        self::assertArrayHasKey('error', $responseData);
        self::assertIsArray($responseData['error']);
    }

    public function testValidateArticleDataReturnsSuccessWhenAllFieldsAreValid(): void
    {
        $articleEntity = (new ArticleEntity())
            ->setArticleNr('12345')
            ->setArticleName('Test Article')
            ->setArticleCategory('Test Category')
            ->setArticleWeight(1.2)
            ->setArticleEan('1234567890123')
            ->setArticleUnit('Stk')
            ->setArticleDepth(100.00)
            ->setArticleWidth(200.00)
            ->setArticleHeight(300.00)
            ->setStockOutStrategy('FIFO')
            ->setStandardLoadingEquipment('BLOCK');

        $responseData = $this->articleValidationService->validateArticleData($articleEntity);

        self::assertArrayHasKey('success', $responseData);
        self::assertTrue($responseData['success']);
    }
}
