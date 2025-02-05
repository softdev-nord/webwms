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

    private int $stockSuId;

    private string $articleNr;

    private float $stockQuantity;

    private string $stockCoordinate;

    private int $stockLn;

    private int $stockFb;

    private int $stockSp;

    private int $stockTf;

    private string $bookingMethod;

    private string $charge;

    private string $loadingEquipment;

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
