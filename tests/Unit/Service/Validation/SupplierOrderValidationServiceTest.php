<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Validation;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\SupplierOrderEntity;
use WebWMS\Service\Validation\SupplierOrderValidationService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        SupplierOrderValidationServiceTest
 */
#[CoversClass(SupplierOrderValidationService::class)]
final class SupplierOrderValidationServiceTest extends TestCase
{
    private SupplierOrderValidationService $supplierOrderValidationService;

    private SupplierOrderEntity $supplierOrderEntity;

    protected function setUp(): void
    {
        $this->supplierOrderValidationService = new SupplierOrderValidationService();
        $this->supplierOrderEntity = new SupplierOrderEntity();
    }

    public function testValidateSupplierOrderDataWithValidData(): void
    {
        $this->supplierOrderEntity->setSupplierOrderNr('123456');
        $this->supplierOrderEntity->setSupplierOrderDate(new DateTimeImmutable());
        $this->supplierOrderEntity->setSupplierOrderCreationDate(new DateTimeImmutable());

        $response = $this->supplierOrderValidationService->validateSupplierOrderData($this->supplierOrderEntity);

        self::assertArrayHasKey('success', $response);
        self::assertTrue($response['success']);
    }

    public function testValidateSupplierOrderDataWithInvalidData(): void
    {
        $this->supplierOrderEntity->setSupplierOrderNr('');
        $this->supplierOrderEntity->setSupplierOrderDate(null);
        $this->supplierOrderEntity->setSupplierOrderCreationDate(null);

        $response = $this->supplierOrderValidationService->validateSupplierOrderData($this->supplierOrderEntity);

        self::assertArrayHasKey('error', $response);
        self::assertEquals('Die Lieferanten-Nr. darf nicht leer sein.', $response['error']['supplierOrderNr']);
        self::assertEquals('Das Bestelldatum darf nicht leer sein.', $response['error']['supplierOrderDate']);
        self::assertEquals('Das Erstellungsdatum der Bestellung darf nicht leer sein.', $response['error']['supplierOrderCreationDate']);
    }
}
