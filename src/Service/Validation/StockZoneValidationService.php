<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\StockZone;

/**
 * @package:    WebWMS\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockZoneValidationService
 */
class StockZoneValidationService
{
    /**
     * @return array<string, array<string, string>|bool|int|string|null>
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateStockZoneData(StockZone $stockZone): array
    {
        $responseData = [];

        if (!$stockZone->getStockZoneShortDesc()) {
            $responseData['error']['stock_zone_short_desc'] = 'Die Kurz-Beschreibung darf nicht leer sein.';
        } else {
            $responseData['stockNr'] = $stockZone->getStockZoneShortDesc();
        }

        if (!$stockZone->getStockZoneDescription()) {
            $responseData['error']['stock_zone_description'] = 'Die Beschreibung darf nicht leer sein.';
        } else {
            $responseData['stockDescription'] = $stockZone->getStockZoneDescription();
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
