<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Validation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockZone;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\Validation\StockZoneValidationService;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service\Validation',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'StockZoneValidationServiceTest'
)]
#[CoversClass(StockZoneValidationService::class)]
final class StockZoneValidationServiceTest extends TestCase
{
    private StockZoneValidationService $stockZoneValidationService;

    protected function setUp(): void
    {
        $this->stockZoneValidationService = new StockZoneValidationService();
    }

    public function testValidateStockZoneDataWithValidData(): void
    {
        $stockZone = new StockZone();
        $stockZone->setStockZoneShortDesc('Short description');
        $stockZone->setStockZoneDescription('Description');

        $result = $this->stockZoneValidationService->validateStockZoneData($stockZone);

        self::assertTrue($result['success']);
        self::assertEquals('Short description', $result['stockNr']);
        self::assertEquals('Description', $result['stockDescription']);
    }

    public function testValidateStockZoneDataWithInvalidData(): void
    {
        $stockZone = new StockZone();
        $stockZone->setStockZoneShortDesc('');
        $stockZone->setStockZoneDescription('');

        $result = $this->stockZoneValidationService->validateStockZoneData($stockZone);

        self::assertEquals('Die Kurz-Beschreibung darf nicht leer sein.', $result['error']['stock_zone_short_desc']);
        self::assertEquals('Die Beschreibung darf nicht leer sein.', $result['error']['stock_zone_description']);
    }
}
