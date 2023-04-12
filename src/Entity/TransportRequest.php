<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package:    WebWMS\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TransportRequest
 */
#[ORM\Table(name: 'transport_request')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\TransportRequestRepository')]
class TransportRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'su_id', type: 'integer', nullable: false)]
    private int $suId;

    #[ORM\Column(name: 'tr_nr', type: 'integer', nullable: false)]
    private int $trNr;

    #[ORM\Column(name: 'tr_pos', type: 'integer', nullable: false)]
    private int $trPos;

    #[ORM\Column(name: 'tr_prio', type: 'integer', nullable: false, options: ['default' => 0])]
    private int $trPrio;

    #[ORM\Column(name: 'article_nr', type: 'string', length: 20, nullable: false)]
    private string $articleNr;

    #[ORM\Column(name: 'tr_quantity', type: 'decimal', precision: 11, scale: 3, nullable: false)]
    private float $trQuantity;

    #[ORM\Column(name: 'stock_coordinate', type: 'string', length: 25, nullable: false)]
    private string $stockCoordinate;

    #[ORM\Column(name: 'stock_nr', type: 'integer', nullable: false)]
    private int $stockNr;

    #[ORM\Column(name: 'stock_level1', type: 'integer', nullable: false)]
    private int $stockLevel1;

    #[ORM\Column(name: 'stock_level2', type: 'integer', nullable: false)]
    private int $stockLevel2;

    #[ORM\Column(name: 'stock_level3', type: 'integer', nullable: false)]
    private int $stockLevel3;

    #[ORM\Column(name: 'stock_level4', type: 'integer', nullable: false)]
    private int $stockLevel4;

//    #[ORM\Column(name: 'to_stock_coordinate', type: 'string', length: 25, nullable: false)]
//    private string $toStockCoordinate;
//
//    #[ORM\Column(name: 'to_stock_nr', type: 'integer', nullable: false)]
//    private int $toStockNr;
//
//    #[ORM\Column(name: 'to_stock_level1', type: 'integer', nullable: false)]
//    private int $toStockLevel1;
//
//    #[ORM\Column(name: 'to_stock_level2', type: 'integer', nullable: false)]
//    private int $toStockLevel2;
//
//    #[ORM\Column(name: 'to_stock_level3', type: 'integer', nullable: false)]
//    private int $toStockLevel3;
//
//    #[ORM\Column(name: 'to_stock_level4', type: 'integer', nullable: false)]
//    private int $toStockLevel4;

    #[ORM\Column(name: 'tr_access', type: 'datetime', nullable: true, options: ['default' => null])]
    private ?\DateTimeInterface $trAccess;

    #[ORM\Column(name: 'tr_dispatch', type: 'datetime', nullable: true, options: ['default' => null])]
    private ?\DateTimeInterface $trDispatch;

    #[ORM\Column(name: 'tr_state', type: 'integer', nullable: false, options: ['default' => '0'])]
    private int $trState;

    #[ORM\Column(name: 'order_username', type: 'string', length: 30, nullable: false)]
    private string $orderUsername;

    #[ORM\Column(name: 'booking_method', type: 'string', length: 10, nullable: false)]
    private string $bookingMethod;

    #[ORM\Column(name: 'doc_id', type: 'integer', nullable: true, options: ['default' => null])]
    private ?int $docId;

    #[ORM\Column(name: 'order_nr', type: 'string', length: 30, nullable: true)]
    private ?string $orderNr;

    #[ORM\Column(name: 'order_pos', type: 'integer', nullable: true)]
    private ?string $orderPos;

    #[ORM\Column(name: 'charge', type: 'string', length: 30, nullable: true, options: ['default' => null])]
    private string $charge;

    #[ORM\Column(name: 'loading_equipment', type: 'string', length: 30, nullable: false)]
    private string $loadingEquipment;

    #[ORM\Column(name: 'confirmation_state', type: 'integer', nullable: true, options: ['default' => null])]
    private ?int $confirmationState;

    #[ORM\Column(name: 'tr_username', type: 'string', length: 30, nullable: true)]
    private ?string $trUsername;

    #[ORM\Column(name: 'tr_computer_ip', type: 'string', length: 30, nullable: false)]
    private string $trComputerIp;

    #[ORM\Column(name: 'tr_blocked', type: 'boolean', nullable: true, options: ['default' => false])]
    private ?bool $trBlocked;

    #[ORM\Column(name: 'tr_start_date', type: 'datetime', nullable: true, options: ['default' => null])]
    private ?\DateTimeInterface $trStartDate;

    #[ORM\Column(name: 'tr_edited', type: 'boolean', nullable: true, options: ['default' => false])]
    private ?bool $trEdited;

    #[ORM\Column(name: 'tr_type', type: 'integer', nullable: false)]
    private int $trType;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getSuId(): int
    {
        return $this->suId;
    }

    public function setSuId(int $suId): self
    {
        $this->suId = $suId;

        return $this;
    }

    public function getTrNr(): int
    {
        return $this->trNr;
    }

    public function setTrNr(int $trNr): self
    {
        $this->trNr = $trNr;

        return $this;
    }

    public function getTrPos(): int
    {
        return $this->trPos;
    }

    public function setTrPos(int $trPos): self
    {
        $this->trPos = $trPos;

        return $this;
    }

    public function getTrPrio(): int
    {
        return $this->trPrio;
    }

    public function setTrPrio(int $trPrio): self
    {
        $this->trPrio = $trPrio;

        return $this;
    }

    public function getArtNr(): string
    {
        return $this->articleNr;
    }

    public function setArtNr(string $articleNr): self
    {
        $this->articleNr = $articleNr;

        return $this;
    }

    public function getTrQuantity(): float
    {
        return $this->trQuantity;
    }

    public function setTrQuantity(float $trQuantity): self
    {
        $this->trQuantity = $trQuantity;

        return $this;
    }

    public function getStockCoordinate(): string
    {
        return $this->stockCoordinate;
    }

    public function setStockCoordinate(string $stockCoordinate): self
    {
        $this->stockCoordinate = $stockCoordinate;

        return $this;
    }

    public function getStockNr(): int
    {
        return $this->stockNr;
    }

    public function setStockNr(int $stockNr): self
    {
        $this->stockNr = $stockNr;

        return $this;
    }

    public function getStockLevel1(): int
    {
        return $this->stockLevel1;
    }

    public function setStockLevel1(int $stockLevel1): self
    {
        $this->stockLevel1 = $stockLevel1;

        return $this;
    }

    public function getStockLevel2(): ?int
    {
        return $this->stockLevel2;
    }

    public function setStockLevel2(int $stockLevel2): self
    {
        $this->stockLevel2 = $stockLevel2;

        return $this;
    }

    public function getStockLevel3(): int
    {
        return $this->stockLevel3;
    }

    public function setStockLevel3(int $stockLevel3): self
    {
        $this->stockLevel3 = $stockLevel3;

        return $this;
    }

    public function getStockLevel4(): int
    {
        return $this->stockLevel4;
    }

    public function setStockLevel4(int $stockLevel4): self
    {
        $this->stockLevel4 = $stockLevel4;

        return $this;
    }
//
//    public function getToStockCoordinate(): string
//    {
//        return $this->toStockCoordinate;
//    }
//
//    public function setToStockCoordinate(string $toStockCoordinate): self
//    {
//        $this->toStockCoordinate = $toStockCoordinate;
//
//        return $this;
//    }
//
//    public function getToStockNr(): int
//    {
//        return $this->toStockNr;
//    }
//
//    public function setToStockNr(int $toStockNr): self
//    {
//        $this->toStockNr = $toStockNr;
//
//        return $this;
//    }
//
//    public function getToStockLevel1(): int
//    {
//        return $this->toStockLevel1;
//    }
//
//    public function setToStockLevel1(int $toStockLevel1): self
//    {
//        $this->toStockLevel1 = $toStockLevel1;
//
//        return $this;
//    }
//
//    public function getToStockLevel2(): int
//    {
//        return $this->toStockLevel2;
//    }
//
//    public function setToStockLevel2(int $toStockLevel2): self
//    {
//        $this->toStockLevel2 = $toStockLevel2;
//
//        return $this;
//    }
//
//    public function getToStockLevel3(): int
//    {
//        return $this->toStockLevel3;
//    }
//
//    public function setToStockLevel3(int $toStockLevel3): self
//    {
//        $this->toStockLevel3 = $toStockLevel3;
//
//        return $this;
//    }
//
//    public function getToStockLevel4(): int
//    {
//        return $this->toStockLevel4;
//    }
//
//    public function setToStockLevel4(int $toStockLevel4): self
//    {
//        $this->toStockLevel4 = $toStockLevel4;
//
//        return $this;
//    }

    public function getTrAccess(): ?\DateTimeInterface
    {
        return $this->trAccess;
    }

    public function setTrAccess(?\DateTimeInterface $trAccess): self
    {
        $this->trAccess = $trAccess;

        return $this;
    }

    public function getTrDispatch(): ?\DateTimeInterface
    {
        return $this->trDispatch;
    }

    public function setTrDispatch(?\DateTimeInterface $trDispatch): self
    {
        $this->trDispatch = $trDispatch;

        return $this;
    }

    public function getTrState(): int
    {
        return $this->trState;
    }

    public function setTrState(int $trState): self
    {
        $this->trState = $trState;

        return $this;
    }

    public function getOrderUsername(): string
    {
        return $this->orderUsername;
    }

    public function setOrderUsername(string $orderUsername): self
    {
        $this->orderUsername = $orderUsername;

        return $this;
    }

    public function getBookingMethod(): string
    {
        return $this->bookingMethod;
    }

    public function setBookingMethod(string $bookingMethod): self
    {
        $this->bookingMethod = $bookingMethod;

        return $this;
    }

    public function getDocId(): ?int
    {
        return $this->docId;
    }

    public function setDocId(int|null $docId): self
    {
        $this->docId = $docId;

        return $this;
    }

    public function getOrderNr(): ?string
    {
        return $this->orderNr;
    }

    public function setOrderNr(string $orderNr): self
    {
        $this->orderNr = $orderNr;

        return $this;
    }

    public function getOrderPos(): ?string
    {
        return $this->orderPos;
    }

    public function setOrderPos(null|string $orderPos): self
    {
        $this->orderPos = $orderPos;

        return $this;
    }

    public function getCharge(): string
    {
        return $this->charge;
    }

    public function setCharge(string $charge): self
    {
        $this->charge = $charge;

        return $this;
    }

    public function getLoadingEquipment(): string
    {
        return $this->loadingEquipment;
    }

    public function setLoadingEquipment(string $loadingEquipment): self
    {
        $this->loadingEquipment = $loadingEquipment;

        return $this;
    }

    public function getConfirmationState(): ?int
    {
        return $this->confirmationState;
    }

    public function setConfirmationState(null|int $confirmationState): self
    {
        $this->confirmationState = $confirmationState;

        return $this;
    }

    public function getTrUsername(): ?string
    {
        return $this->trUsername;
    }

    public function setTrUsername(null|string $trUsername): self
    {
        $this->trUsername = $trUsername;

        return $this;
    }

    public function getTrComputerIp(): string
    {
        return $this->trComputerIp;
    }

    public function setTrComputerIp(string $trComputerIp): self
    {
        $this->trComputerIp = $trComputerIp;

        return $this;
    }

    public function getTrBlocked(): ?bool
    {
        return $this->trBlocked;
    }

    public function setTrBlocked(null|bool $trBlocked): self
    {
        $this->trBlocked = $trBlocked;

        return $this;
    }

    public function getTrStartDate(): ?\DateTimeInterface
    {
        return $this->trStartDate;
    }

    public function setTrStartDate(\DateTimeInterface $trStartDate): self
    {
        $this->trStartDate = $trStartDate;

        return $this;
    }

    public function getTrEdited(): ?bool
    {
        return $this->trEdited;
    }

    public function setTrEdited(null|bool $trEdited): self
    {
        $this->trEdited = $trEdited;

        return $this;
    }

    public function getTrType(): int
    {
        return $this->trType;
    }

    public function setTrType(int $trType): self
    {
        $this->trType = $trType;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
