<?php

declare(strict_types=1);

namespace WebWMS\Dto;

use WebWMS\Helper\HydrateStaticTrait;

/**
 * @package:    WebWMS\Dto
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        OccupiedStockLocationDto
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class OccupiedStockLocationDto
{
    use HydrateStaticTrait;

    public ?int $id = null;

    public ?string $koordinate = null;

    public ?int $ln = null;

    public ?int $fb = null;

    public ?int $sp = null;

    public ?int $tf = null;

    public ?string $stockLocationDesc = null;

    public ?string $inStock = null;

    public ?string $incomingStock = null;

    public ?string $reservedStock = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKoordinate(): ?string
    {
        return $this->koordinate;
    }

    public function getLn(): ?int
    {
        return $this->ln;
    }

    public function getFb(): ?int
    {
        return $this->fb;
    }

    public function getSp(): ?int
    {
        return $this->sp;
    }

    public function getTf(): ?int
    {
        return $this->tf;
    }

    public function getStockLocationDesc(): ?string
    {
        return $this->stockLocationDesc;
    }

    public function getInStock(): ?string
    {
        return $this->inStock;
    }

    public function getIncomingStock(): ?string
    {
        return $this->incomingStock;
    }

    public function getReservedStock(): ?string
    {
        return $this->reservedStock;
    }
}
