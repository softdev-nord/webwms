<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Validation;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Service\Validation\StockLayoutValidationService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLayoutValidationServiceTest
 */
#[CoversClass(StockLayoutValidationService::class)]
final class StockLayoutValidationServiceTest extends TestCase
{
    private StockLayoutValidationService $stockLayoutValidationService;

    #[Override]
    protected function setUp(): void
    {
        $this->stockLayoutValidationService = new StockLayoutValidationService();
    }

    public function testValidateStockLayoutDataReturnsErrorWhenRequiredFieldsAreEmpty(): void
    {
        $requestData = [
            'stockNr' => '',
            'stockDescription' => '',
            'stockLevel1' => '',
            'stockLevel2' => '',
            'stockLevel3' => '',
            'stockLevel4' => '',
            'stockModel' => '',
            'stockTyp' => '',
            'stockLongDescription' => '',
        ];

        $responseData = $this->stockLayoutValidationService->validateStockLayoutData($requestData);

        self::assertArrayHasKey('error', $responseData);
        self::assertIsArray($responseData['error']);
    }

    public function testValidateStockLayoutDataReturnsSuccessWhenAllFieldsAreValid(): void
    {
        $requestData = [
            'stockNr' => 100,
            'stockDescription' => 'Block',
            'stockLevel1' => 1,
            'stockLevel2' => 1,
            'stockLevel3' => 1,
            'stockLevel4' => 1,
            'stockModel' => 'L2',
            'stockTyp' => 'BLL',
            'stockLongDescription' => 'Block',
        ];

        $responseData = $this->stockLayoutValidationService->validateStockLayoutData($requestData);

        self::assertArrayHasKey('success', $responseData);
        self::assertTrue($responseData['success']);
    }
}
