<?php

declare(strict_types=1);

namespace WebWMS\Dto;

use WebWMS\Helper\HydrateStaticTrait;

class TransportRequestDto
{
    use HydrateStaticTrait;

    public ?int $id = null;
    public ?int $suId = null;
    public ?int $trNr = null;
    public ?int $trPos = null;
    public ?int $trPrio = null;
    public ?string $articleNr = null;
    public ?string $trQuantity = null;
    public ?string $stockCoordinate = null;
    public ?string $stockLocation = null;
    public ?int $trState = null;
    public ?string $orderUsername = null;
    public ?string $bookingMethod = null;
    public ?string $orderNr = null;
    public ?string $loadingEquipment = null;
    public ?string $trUsername = null;
    public ?string $trComputerIp = null;
    public ?int $trBlocked = null;
    public ?string $trStartDate = null;
    public ?int $trEdited = null;
    public ?int $trType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSuId(): ?int
    {
        return $this->suId;
    }

    public function getTrNr(): ?int
    {
        return $this->trNr;
    }

    public function getTrPos(): ?int
    {
        return $this->trPos;
    }

    public function getTrPrio(): ?int
    {
        return $this->trPrio;
    }

    public function getArticleNr(): ?string
    {
        return $this->articleNr;
    }

    public function getTrQuantity(): ?string
    {
        return $this->trQuantity;
    }

    public function getStockCoordinate(): ?string
    {
        return $this->stockCoordinate;
    }

    public function getStockLocation(): ?string
    {
        return $this->stockLocation;
    }

    public function getTrState(): ?int
    {
        return $this->trState;
    }

    public function getOrderUsername(): ?string
    {
        return $this->orderUsername;
    }

    public function getBookingMethod(): ?string
    {
        return $this->bookingMethod;
    }

    public function getOrderNr(): ?string
    {
        return $this->orderNr;
    }

    public function getLoadingEquipment(): ?string
    {
        return $this->loadingEquipment;
    }

    public function getTrUsername(): ?string
    {
        return $this->trUsername;
    }

    public function getTrComputerIp(): ?string
    {
        return $this->trComputerIp;
    }

    public function getTrBlocked(): ?int
    {
        return $this->trBlocked;
    }

    public function getTrStartDate(): ?string
    {
        return $this->trStartDate;
    }

    public function getTrEdited(): ?int
    {
        return $this->trEdited;
    }

    public function getTrType(): ?int
    {
        return $this->trType;
    }
}
