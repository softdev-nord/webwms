<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\StockZone;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Service\Validation',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockZoneValidationService'
)]
class StockZoneValidationService
{
    /**
     * @return array<string, array<string, string>|bool|int|string|null>
     *
     * @SuppressWarnings(CyclomaticComplexity)
     * @SuppressWarnings(NPathComplexity)
     * @SuppressWarnings(ElseExpression)
     */
    public function validateStockZoneData(StockZone $stockZone): array
    {
        $responseData = [];

        if ($stockZone->getStockZoneShortDesc() === '' || $stockZone->getStockZoneShortDesc() === '0') {
            $responseData['error']['stock_zone_short_desc'] = 'Die Kurz-Beschreibung darf nicht leer sein.';
        } else {
            $responseData['stockNr'] = $stockZone->getStockZoneShortDesc();
        }

        if (in_array($stockZone->getStockZoneDescription(), [null, '', '0'], true)) {
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
