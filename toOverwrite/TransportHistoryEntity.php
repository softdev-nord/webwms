<?php

declare(strict_types=1);

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\TransportHistoryRepository;

/**
 * @package:    WebWMS\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        TransportHistoryEntity
 */
#[ORM\Table(name: 'transport_history')]
#[ORM\Entity(repositoryClass: TransportHistoryRepository::class)]
class TransportHistoryEntityChange
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(name: 'su_id', type: Types::INTEGER, nullable: false)]
    private int $suId;

    #[ORM\Column(name: 'tr_nr', type: Types::INTEGER, nullable: false)]
    private int $trNr;

    #[ORM\Column(name: 'tr_pos', type: Types::INTEGER, nullable: false)]
    private int $trPos;

    #[ORM\Column(name: 'tr_prio', type: Types::INTEGER, nullable: false, options: ['default' => 0])]
    private int $trPrio;

    #[ORM\Column(name: 'article_nr', type: Types::STRING, length: 20, nullable: false)]
    private string $articleNr;

    #[ORM\Column(name: 'tr_quantity', type: Types::DECIMAL, precision: 11, scale: 3, nullable: false)]
    private float $trQuantity;

    #[ORM\Column(name: 'from_stock_coordinate', type: Types::STRING, length: 25, nullable: false)]
    private string $fromStockCoordinate;

    #[ORM\Column(name: 'from_stock_nr', type: Types::INTEGER, nullable: false)]
    private int $fromStockNr;

    #[ORM\Column(name: 'from_stock_level1', type: Types::INTEGER, nullable: false)]
    private int $fromStockLevel1;

    #[ORM\Column(name: 'from_stock_level2', type: Types::INTEGER, nullable: false)]
    private int $fromStockLevel2;

    #[ORM\Column(name: 'from_stock_level3', type: Types::INTEGER, nullable: false)]
    private int $fromStockLevel3;

    #[ORM\Column(name: 'from_stock_level4', type: Types::INTEGER, nullable: false)]
    private int $fromStockLevel4;

    #[ORM\Column(name: 'to_stock_coordinate', type: Types::STRING, length: 25, nullable: false)]
    private string $toStockCoordinate;

    #[ORM\Column(name: 'to_stock_nr', type: Types::INTEGER, nullable: false)]
    private int $toStockNr;

    #[ORM\Column(name: 'to_stock_level1', type: Types::INTEGER, nullable: false)]
    private int $toStockLevel1;

    #[ORM\Column(name: 'to_stock_level2', type: Types::INTEGER, nullable: false)]
    private int $toStockLevel2;

    #[ORM\Column(name: 'to_stock_level3', type: Types::INTEGER, nullable: false)]
    private int $toStockLevel3;

    #[ORM\Column(name: 'to_stock_level4', type: Types::INTEGER, nullable: false)]
    private int $toStockLevel4;

    #[ORM\Column(name: 'tr_access', type: Types::DATETIME_MUTABLE, nullable: true, options: ['default' => null])]
    private ?DateTimeInterface $trAccess = null;

    #[ORM\Column(name: 'tr_dispatch', type: Types::DATETIME_MUTABLE, nullable: true, options: ['default' => null])]
    private ?DateTimeInterface $trDispatch = null;

    #[ORM\Column(name: 'tr_state', type: Types::INTEGER, nullable: false, options: ['default' => '0'])]
    private int $trState;

    #[ORM\Column(name: 'order_username', type: Types::STRING, length: 30, nullable: false)]
    private string $orderUsername;

    #[ORM\Column(name: 'booking_method', type: Types::STRING, length: 10, nullable: false)]
    private string $bookingMethod;

    #[ORM\Column(name: 'doc_id', type: Types::INTEGER, nullable: true, options: ['default' => null])]
    private ?int $docId = null;

    #[ORM\Column(name: 'order_nr', type: Types::STRING, length: 30, nullable: true)]
    private ?string $orderNr = null;

    #[ORM\Column(name: 'order_pos', type: Types::INTEGER, nullable: true)]
    private ?int $orderPos = null;

    #[ORM\Column(name: 'charge', type: Types::STRING, length: 30, nullable: true, options: ['default' => null])]
    private ?string $charge = null;

    #[ORM\Column(name: 'loading_equipment', type: Types::STRING, length: 30, nullable: false)]
    private string $loadingEquipment;

    #[ORM\Column(name: 'confirmation_state', type: Types::INTEGER, nullable: true, options: ['default' => null])]
    private ?int $confirmationState = null;

    #[ORM\Column(name: 'tr_username', type: Types::STRING, length: 30, nullable: true)]
    private ?string $trUsername = null;

    #[ORM\Column(name: 'tr_computer_ip', type: Types::STRING, length: 30, nullable: false)]
    private string $trComputerIp;

    #[ORM\Column(name: 'tr_blocked', type: Types::BOOLEAN, nullable: true, options: ['default' => false])]
    private ?bool $trBlocked = null;

    #[ORM\Column(name: 'tr_start_date', type: Types::DATETIME_MUTABLE, nullable: true, options: ['default' => null])]
    private ?DateTimeInterface $trStartDate = null;

    #[ORM\Column(name: 'tr_edited', type: Types::BOOLEAN, nullable: true, options: ['default' => false])]
    private ?bool $trEdited = null;

    #[ORM\Column(name: 'tr_type', type: Types::INTEGER, nullable: false)]
    private int $trType;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $updatedAt = null;

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

    public function getArticleNr(): string
    {
        return $this->articleNr;
    }

    public function setArticleNr(string $articleNr): self
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

    public function getFromStockCoordinate(): string
    {
        return $this->fromStockCoordinate;
    }

    public function setFromStockCoordinate(string $fromStockCoordinate): self
    {
        $this->fromStockCoordinate = $fromStockCoordinate;

        return $this;
    }

    public function getFromStockNr(): int
    {
        return $this->fromStockNr;
    }

    public function setFromStockNr(int $fromStockNr): self
    {
        $this->fromStockNr = $fromStockNr;

        return $this;
    }

    public function getFromStockLevel1(): int
    {
        return $this->fromStockLevel1;
    }

    public function setFromStockLevel1(int $fromStockLevel1): self
    {
        $this->fromStockLevel1 = $fromStockLevel1;

        return $this;
    }

    public function getFromStockLevel2(): int
    {
        return $this->fromStockLevel2;
    }

    public function setFromStockLevel2(int $fromStockLevel2): self
    {
        $this->fromStockLevel2 = $fromStockLevel2;

        return $this;
    }

    public function getFromStockLevel3(): int
    {
        return $this->fromStockLevel3;
    }

    public function setFromStockLevel3(int $fromStockLevel3): self
    {
        $this->fromStockLevel3 = $fromStockLevel3;

        return $this;
    }

    public function getFromStockLevel4(): int
    {
        return $this->fromStockLevel4;
    }

    public function setFromStockLevel4(int $fromStockLevel4): self
    {
        $this->fromStockLevel4 = $fromStockLevel4;

        return $this;
    }

    public function getToStockCoordinate(): string
    {
        return $this->toStockCoordinate;
    }

    public function setToStockCoordinate(string $toStockCoordinate): self
    {
        $this->toStockCoordinate = $toStockCoordinate;

        return $this;
    }

    public function getToStockNr(): int
    {
        return $this->toStockNr;
    }

    public function setToStockNr(int $toStockNr): self
    {
        $this->toStockNr = $toStockNr;

        return $this;
    }

    public function getToStockLevel1(): int
    {
        return $this->toStockLevel1;
    }

    public function setToStockLevel1(int $toStockLevel1): self
    {
        $this->toStockLevel1 = $toStockLevel1;

        return $this;
    }

    public function getToStockLevel2(): int
    {
        return $this->toStockLevel2;
    }

    public function setToStockLevel2(int $toStockLevel2): self
    {
        $this->toStockLevel2 = $toStockLevel2;

        return $this;
    }

    public function getToStockLevel3(): int
    {
        return $this->toStockLevel3;
    }

    public function setToStockLevel3(int $toStockLevel3): self
    {
        $this->toStockLevel3 = $toStockLevel3;

        return $this;
    }

    public function getToStockLevel4(): int
    {
        return $this->toStockLevel4;
    }

    public function setToStockLevel4(int $toStockLevel4): self
    {
        $this->toStockLevel4 = $toStockLevel4;

        return $this;
    }

    public function getTrAccess(): ?DateTimeInterface
    {
        return $this->trAccess;
    }

    public function setTrAccess(?DateTimeInterface $trAccess): self
    {
        $this->trAccess = $trAccess;

        return $this;
    }

    public function getTrDispatch(): ?DateTimeInterface
    {
        return $this->trDispatch;
    }

    public function setTrDispatch(?DateTimeInterface $trDispatch): self
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

    public function setDocId(?int $docId): self
    {
        $this->docId = $docId;

        return $this;
    }

    public function getOrderNr(): ?string
    {
        return $this->orderNr;
    }

    public function setOrderNr(?string $orderNr): self
    {
        $this->orderNr = $orderNr;

        return $this;
    }

    public function getOrderPos(): ?int
    {
        return $this->orderPos;
    }

    public function setOrderPos(?int $orderPos): self
    {
        $this->orderPos = $orderPos;

        return $this;
    }

    public function getCharge(): ?string
    {
        return $this->charge;
    }

    public function setCharge(?string $charge): self
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

    public function setConfirmationState(?int $confirmationState): self
    {
        $this->confirmationState = $confirmationState;

        return $this;
    }

    public function getTrUsername(): ?string
    {
        return $this->trUsername;
    }

    public function setTrUsername(?string $trUsername): self
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

    public function setTrBlocked(?bool $trBlocked): self
    {
        $this->trBlocked = $trBlocked;

        return $this;
    }

    public function getTrStartDate(): ?DateTimeInterface
    {
        return $this->trStartDate;
    }

    public function setTrStartDate(?DateTimeInterface $trStartDate): self
    {
        $this->trStartDate = $trStartDate;

        return $this;
    }

    public function getTrEdited(): ?bool
    {
        return $this->trEdited;
    }

    public function setTrEdited(?bool $trEdited): self
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

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
