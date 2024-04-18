<?php

declare(strict_types=1);

namespace WebWMS\Dto;

use WebWMS\Helper\HydrateStaticTrait;

/**
 * @package:    WebWMS\Dto
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        FreeStockLocationDto
 */
class FreeStockLocationDto
{
    use HydrateStaticTrait;

    public ?int $id = null;

    public ?int $suId = null;

    public ?int $ln = null;

    public ?int $fb = null;

    public ?int $sp = null;

    public ?int $tf = null;

    public ?string $lnKomplett = null;

    public ?string $koordinate = null;

    public ?string $system = null;

    public ?string $quantity = null;

    public ?string $leQuantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getSuId(): ?int
    {
        return $this->suId;
    }

    public function setSuId(?int $suId): self
    {
        $this->suId = $suId;

        return $this;
    }

    public function getLn(): ?int
    {
        return $this->ln;
    }

    public function setLn(?int $ln): self
    {
        $this->ln = $ln;

        return $this;
    }

    public function getFb(): ?int
    {
        return $this->fb;
    }

    public function setFb(?int $fb): self
    {
        $this->fb = $fb;

        return $this;
    }

    public function getSp(): ?int
    {
        return $this->sp;
    }

    public function setSp(?int $sp): self
    {
        $this->sp = $sp;

        return $this;
    }

    public function getTf(): ?int
    {
        return $this->tf;
    }

    public function setTf(?int $tf): self
    {
        $this->tf = $tf;

        return $this;
    }

    public function getLnKomplett(): ?string
    {
        return $this->lnKomplett;
    }

    public function setLnKomplett(?string $lnKomplett): self
    {
        $this->lnKomplett = $lnKomplett;

        return $this;
    }

    public function getKoordinate(): ?string
    {
        return $this->koordinate;
    }

    public function setKoordinate(?string $koordinate): self
    {
        $this->koordinate = $koordinate;

        return $this;
    }

    public function getSystem(): ?string
    {
        return $this->system;
    }

    public function setSystem(?string $system): self
    {
        $this->system = $system;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(?string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getLeQuantity(): ?string
    {
        return $this->leQuantity;
    }

    public function setLeQuantity(?string $leQuantity): self
    {
        $this->leQuantity = $leQuantity;

        return $this;
    }
}
