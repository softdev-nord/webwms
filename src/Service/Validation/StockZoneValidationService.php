<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\StockZoneEntity;

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
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateStockZoneData(StockZoneEntity $stockZoneEntity): array
    {
        $responseData = [];

        if ($stockZoneEntity->getStockZoneShortDesc() === '' || $stockZoneEntity->getStockZoneShortDesc() === '0') {
            $responseData['error']['stock_zone_short_desc'] = 'Die Kurz-Beschreibung darf nicht leer sein.';
        } else {
            $responseData['stockNr'] = $stockZoneEntity->getStockZoneShortDesc();
        }

        if ($stockZoneEntity->getStockZoneDescription() === null || $stockZoneEntity->getStockZoneDescription() === '' || $stockZoneEntity->getStockZoneDescription() === '0') {
            $responseData['error']['stock_zone_description'] = 'Die Beschreibung darf nicht leer sein.';
        } else {
            $responseData['stockDescription'] = $stockZoneEntity->getStockZoneDescription();
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
