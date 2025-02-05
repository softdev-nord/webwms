<?php

declare(strict_types=1);

namespace WebWMS\Dto;

use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Helper\HydrateStaticTrait;

#[ClassInformation(
    package: 'WebWMS\Dto',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockLocationValuesDto'
)]
class StockLocationValuesDto
{
    use HydrateStaticTrait;

    public string $stockLocationLn;

    public string $stockLocationFb;

    public string $stockLocationSp;

    public string $stockLocationTf;

    public string $stockLocationDesc;

    public string $stockLocationZone;

    public float $stockLocationWidth;

    public float $stockLocationDepth;

    public float $stockLocationHeight;

    public function getStockLocationLn(): string
    {
        return $this->stockLocationLn;
    }

    public function getStockLocationFb(): string
    {
        return $this->stockLocationFb;
    }

    public function getStockLocationSp(): string
    {
        return $this->stockLocationSp;
    }

    public function getStockLocationTf(): string
    {
        return $this->stockLocationTf;
    }

    public function getStockLocationDesc(): string
    {
        return $this->stockLocationDesc;
    }

    public function getStockLocationZone(): string
    {
        return $this->stockLocationZone;
    }

    public function getStockLocationWidth(): float
    {
        return $this->stockLocationWidth;
    }

    public function getStockLocationDepth(): float
    {
        return $this->stockLocationDepth;
    }

    public function getStockLocationHeight(): float
    {
        return $this->stockLocationHeight;
    }
}
