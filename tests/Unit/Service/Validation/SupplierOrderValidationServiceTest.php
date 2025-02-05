<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Validation;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\Validation\SupplierOrderValidationService;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service\Validation',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'SupplierOrderValidationServiceTest'
)]
#[CoversClass(SupplierOrderValidationService::class)]
final class SupplierOrderValidationServiceTest extends TestCase
{
    private SupplierOrderValidationService $supplierOrderValidationService;

    private SupplierOrder $supplierOrder;

    protected function setUp(): void
    {
        $this->supplierOrderValidationService = new SupplierOrderValidationService();
        $this->supplierOrder = new SupplierOrder();
    }

    public function testValidateSupplierOrderDataWithValidData(): void
    {
        $this->supplierOrder->setSupplierOrderNr('123456');
        $this->supplierOrder->setSupplierOrderDate(new DateTimeImmutable());
        $this->supplierOrder->setSupplierOrderCreationDate(new DateTimeImmutable());

        $response = $this->supplierOrderValidationService->validateSupplierOrderData($this->supplierOrder);

        self::assertArrayHasKey('success', $response);
        self::assertTrue($response['success']);
    }

    public function testValidateSupplierOrderDataWithInvalidData(): void
    {
        $this->supplierOrder->setSupplierOrderNr('');
        $this->supplierOrder->setSupplierOrderDate(null);
        $this->supplierOrder->setSupplierOrderCreationDate(null);

        $response = $this->supplierOrderValidationService->validateSupplierOrderData($this->supplierOrder);

        self::assertArrayHasKey('error', $response);
        self::assertEquals('Die Lieferanten-Nr. darf nicht leer sein.', $response['error']['supplierOrderNr']);
        self::assertEquals('Das Bestelldatum darf nicht leer sein.', $response['error']['supplierOrderDate']);
        self::assertEquals('Das Erstellungsdatum der Bestellung darf nicht leer sein.', $response['error']['supplierOrderCreationDate']);
    }
}
