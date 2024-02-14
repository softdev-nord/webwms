<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Validation;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
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
        $requestData = [
            'articleNr' => '',
            'articleName' => '',
            'articleCategory' => '',
            'articleWeight' => '',
            'articleEan' => '',
            'articleUnit' => '',
            'articleDepth' => '',
            'articleWidth' => '',
            'articleHeight' => '',
            'stockOutStrategy' => '',
            'standardLoadingEquipment' => '',
        ];

        $responseData = $this->articleValidationService->validateArticleData($requestData);

        self::assertArrayHasKey('error', $responseData);
        self::assertIsArray($responseData['error']);
    }

    public function testValidateArticleDataReturnsSuccessWhenAllFieldsAreValid(): void
    {
        $requestData = [
            'articleNr' => '12345',
            'articleName' => 'Test ArticleController',
            'articleCategory' => 'Test Category',
            'articleWeight' => '1.2',
            'articleEan' => '1234567890123',
            'articleUnit' => 'Stk',
            'articleDepth' => '100',
            'articleWidth' => '200',
            'articleHeight' => '300',
            'stockOutStrategy' => 'FIFO',
            'standardLoadingEquipment' => 'BLOCK',
        ];

        $responseData = $this->articleValidationService->validateArticleData($requestData);

        self::assertArrayHasKey('success', $responseData);
        self::assertTrue($responseData['success']);
    }
}
