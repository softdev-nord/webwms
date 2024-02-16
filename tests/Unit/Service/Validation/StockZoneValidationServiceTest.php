<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Validation;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\StockZoneEntity;
use WebWMS\Service\Validation\StockZoneValidationService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockZoneValidationServiceTest
 */
#[CoversClass(StockZoneValidationService::class)]
final class StockZoneValidationServiceTest extends TestCase
{
    private StockZoneValidationService $stockZoneValidationService;

    #[Override]
    protected function setUp(): void
    {
        $this->stockZoneValidationService = new StockZoneValidationService();
    }

    public function testValidateStockZoneDataWithValidData(): void
    {
        $stockZoneEntity = new StockZoneEntity();
        $stockZoneEntity->setStockZoneShortDesc('Short description');
        $stockZoneEntity->setStockZoneDescription('Description');

        $result = $this->stockZoneValidationService->validateStockZoneData($stockZoneEntity);

        self::assertTrue($result['success']);
        self::assertEquals('Short description', $result['stockNr']);
        self::assertEquals('Description', $result['stockDescription']);
    }

    public function testValidateStockZoneDataWithInvalidData(): void
    {
        $stockZoneEntity = new StockZoneEntity();
        $stockZoneEntity->setStockZoneShortDesc('');
        $stockZoneEntity->setStockZoneDescription('');

        $result = $this->stockZoneValidationService->validateStockZoneData($stockZoneEntity);

        self::assertEquals('Die Kurz-Beschreibung darf nicht leer sein.', $result['error']['stock_zone_short_desc']);
        self::assertEquals('Die Beschreibung darf nicht leer sein.', $result['error']['stock_zone_description']);
    }
}
