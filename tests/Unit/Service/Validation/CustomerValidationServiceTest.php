<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Validation;

use PHPUnit\Framework\TestCase;
use WebWMS\Service\Validation\CustomerValidationService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerValidationServiceTest
 *
 * @covers \WebWMS\Service\Validation\CustomerValidationService
 */
final class CustomerValidationServiceTest extends TestCase
{
    private CustomerValidationService $customerValidationService;

    protected function setUp(): void
    {
        $this->customerValidationService = new CustomerValidationService();
    }

    public function testValidateCustomerDataReturnsErrorWhenRequiredFieldsAreEmpty(): void
    {
        $requestData = [
            'customerNr' => '',
            'customerName' => '',
            'customerAddressStreet' => '',
            'customerAddressStreetNr' => '',
            'customerCountryCode' => '',
            'customerZipCode' => '',
            'customerCity' => '',
        ];

        $responseData = $this->customerValidationService->validateCustomerData($requestData);

        self::assertArrayHasKey('error', $responseData);
        self::assertIsArray($responseData['error']);
    }

    public function testValidateCustomerDataReturnsSuccessWhenAllFieldsAreValid(): void
    {
        $requestData = [
            'customerNr' => '123456',
            'customerName' => 'Test Customer',
            'customerAddressStreet' => 'Test Straße',
            'customerAddressStreetNr' => 'Test Hausnummer',
            'customerCountryCode' => 'DE',
            'customerZipCode' => '12345',
            'customerCity' => 'Test Stadt',

        ];

        $responseData = $this->customerValidationService->validateCustomerData($requestData);

        self::assertArrayHasKey('success', $responseData);
        self::assertTrue($responseData['success']);
    }
}
