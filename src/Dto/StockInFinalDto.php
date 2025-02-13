<?php

declare(strict_types=1);

namespace WebWMS\Dto;

use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Helper\HydrateStaticTrait;

#[ClassInformation(
    package: 'WebWMS\Dto',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockInFinalDto'
)]
class StockInFinalDto
{
    use HydrateStaticTrait;

    public int $stockSuId;

    public string $articleNr;

    public float $stockQuantity;

    public string $stockCoordinate;

    public int $stockLn;

    public int $stockFb;

    public int $stockSp;

    public int $stockTf;

    public string $bookingMethod;

    public string $charge;

    public string $loadingEquipment;

    public function getStockSuId(): int
    {
        return $this->stockSuId;
    }

    public function getArticleNr(): string
    {
        return $this->articleNr;
    }

    public function getStockQuantity(): float
    {
        return $this->stockQuantity;
    }

    public function getStockCoordinate(): string
    {
        return $this->stockCoordinate;
    }

    public function getStockLn(): int
    {
        return $this->stockLn;
    }

    public function getStockFb(): int
    {
        return $this->stockFb;
    }

    public function getStockSp(): int
    {
        return $this->stockSp;
    }

    public function getStockTf(): int
    {
        return $this->stockTf;
    }

    public function getBookingMethod(): string
    {
        return $this->bookingMethod;
    }

    public function getCharge(): string
    {
        return $this->charge;
    }

    public function getLoadingEquipment(): string
    {
        return $this->loadingEquipment;
    }
}
