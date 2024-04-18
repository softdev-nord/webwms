<?php

declare(strict_types=1);

namespace WebWMS\Dto;

use WebWMS\Helper\HydrateStaticTrait;

/**
 * @package:    WebWMS\Dto
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLocationDto
 */
class StockLocationDto
{
    use HydrateStaticTrait;

    public ?int $id = null;

    public ?int $ln = null;

    public ?int $fb = null;

    public ?int $sp = null;

    public ?int $tf = null;

    public ?string $lnKomplett = null;

    public ?string $koordinate = null;

    public ?string $system = null;

    public bool $belegt = false;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLnKomplett(): ?string
    {
        return $this->lnKomplett;
    }

    public function getKoordinate(): ?string
    {
        return $this->koordinate;
    }

    public function getSystem(): ?string
    {
        return $this->system;
    }

    public function isBelegt(): bool
    {
        return $this->belegt;
    }
}
