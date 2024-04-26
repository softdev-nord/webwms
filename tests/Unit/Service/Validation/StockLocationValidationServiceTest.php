<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Validation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Service\Validation\StockLocationValidationService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        StockLocationValidationServiceTest
 */
#[CoversClass(StockLocationValidationService::class)]
final class StockLocationValidationServiceTest extends TestCase
{
    private StockLocationValidationService $stockLocationValidationService;

    protected function setUp(): void
    {
        $this->stockLocationValidationService = new StockLocationValidationService();
    }

    public function testValidateStockLocationDataReturnsErrorWhenRequiredFieldsAreEmpty(): void
    {
        $requestData = [
            'stockLocationLn' => '',
            'stockLocationFb' => '',
            'stockLocationSp' => '',
            'stockLocationTf' => '',
            'stockLocationDesc' => '',
            'stockLocationWidth' => '',
            'stockLocationDepth' => '',
            'stockLocationHeight' => '',
        ];

        $responseData = $this->stockLocationValidationService->validateStockLocationData($requestData);

        self::assertArrayHasKey('error', $responseData);
        self::assertIsArray($responseData['error']);
    }

    public function testValidateStockLocationDataReturnsSuccessWhenAllFieldsAreValid(): void
    {
        $requestData = [
            'stockLocationLn' => 100,
            'stockLocationFb' => 1,
            'stockLocationSp' => 1,
            'stockLocationTf' => 1,
            'stockLocationDesc' => 'Block-Lager',
            'stockLocationWidth' => 800.00,
            'stockLocationDepth' => 1200.00,
            'stockLocationHeight' => 2000.00,
        ];

        $responseData = $this->stockLocationValidationService->validateStockLocationData($requestData);

        self::assertArrayHasKey('success', $responseData);
        self::assertTrue($responseData['success']);
    }
}
