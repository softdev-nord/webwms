<?php

declare(strict_types=1);

namespace WebWMS\Dto;

use WebWMS\Helper\HydrateStaticTrait;

/**
 * @package:    WebWMS\Dto
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockInDto
 */
class StockInDto
{
    use HydrateStaticTrait;

    public ?string $articleId = null;

    public ?string $articleNr = null;

    public ?string $standardLoadingEquipment = null;

    public ?string $leQuantity = null;

    public ?string $quantity = null;

    public ?string $charge = null;

    public ?int $stockLocationId = null;

    public function getArticleId(): ?string
    {
        return $this->articleId;
    }

    public function getArticleNr(): ?string
    {
        return $this->articleNr;
    }

    public function getStandardLoadingEquipment(): ?string
    {
        return $this->standardLoadingEquipment;
    }

    public function getLeQuantity(): ?string
    {
        return $this->leQuantity;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function getCharge(): ?string
    {
        return $this->charge;
    }

    public function getStockLocationId(): ?int
    {
        return $this->stockLocationId;
    }
}
