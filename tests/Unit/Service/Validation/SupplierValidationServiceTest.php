<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Validation;

use PHPUnit\Framework\TestCase;
use WebWMS\Service\Validation\SupplierValidationService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierValidationServiceTest
 *
 * @covers \WebWMS\Service\Validation\SupplierValidationService
 */
final class SupplierValidationServiceTest extends TestCase
{
    private SupplierValidationService $supplierValidationService;

    protected function setUp(): void
    {
        $this->supplierValidationService = new SupplierValidationService();
    }

    public function testValidateSupplierDataReturnsErrorWhenRequiredFieldsAreEmpty(): void
    {
        $requestData = [
            'supplierNr' => '',
            'supplierName' => '',
            'supplierAddressStreet' => '',
            'supplierAddressStreetNr' => '',
            'supplierAddressCountryCode' => '',
            'supplierAddressZipcode' => '',
            'supplierAddressCity' => '',
        ];

        $responseData = $this->supplierValidationService->validateSupplierData($requestData);

        self::assertArrayHasKey('error', $responseData);
        self::assertIsArray($responseData['error']);
    }

    public function testValidateSupplierDataReturnsSuccessWhenAllFieldsAreValid(): void
    {
        $requestData = [
            'supplierNr' => '123456',
            'supplierName' => 'Test Supplier',
            'supplierAddressStreet' => 'Test Straße',
            'supplierAddressStreetNr' => 'Test Hausnummer',
            'supplierAddressCountryCode' => 'DE',
            'supplierAddressZipcode' => '12345',
            'supplierAddressCity' => 'Test Stadt',

        ];

        $responseData = $this->supplierValidationService->validateSupplierData($requestData);

        self::assertArrayHasKey('success', $responseData);
        self::assertTrue($responseData['success']);
    }
}
