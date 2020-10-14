<?php

namespace WebWMS\Entity;

use WebWMS\Repository\TransportHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransportHistoryRepository::class)
 */
class TransportHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $su_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $tr_nr;

    /**
     * @ORM\Column(type="integer")
     */
    private $tr_pos;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $tr_prio;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $art_nr;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=3)
     */
    private $tr_quantity;

    /**
     * @ORM\Column(type="decimal", precision=25, scale=0)
     */
    private $stock_coordinate;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock_nr;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock_level1;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock_level2;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock_level3;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock_level4;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"default": NULL})
     */
    private $tr_access;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"default": NULL})
     */
    private $tr_dispatch;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tr_state;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $order_username;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $booking_method;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $order_nr;

    /**
     * @ORM\Column(type="integer")
     */
    private $order_pos;

    /**
     * @ORM\Column(type="string", length=20, options={"default": NULL})
     */
    private $charge;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $loading_equipment;

    /**
     * @ORM\Column(type="integer")
     */
    private $confirmation_state;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $tr_username;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $tr_computer_ip;

    /**
     * @ORM\Column(type="integer")
     */
    private $tr_blocked;

    /**
     * @ORM\Column(type="datetime")
     */
    private $tr_start_date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tr_edited;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $tr_typ;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSuId(): ?int
    {
        return $this->su_id;
    }

    public function setSuId(int $su_id): self
    {
        $this->su_id = $su_id;

        return $this;
    }

    public function getTrNr(): ?int
    {
        return $this->tr_nr;
    }

    public function setTrNr(int $tr_nr): self
    {
        $this->tr_nr = $tr_nr;

        return $this;
    }

    public function getTrPos(): ?int
    {
        return $this->tr_pos;
    }

    public function setTrPos(int $tr_pos): self
    {
        $this->tr_pos = $tr_pos;

        return $this;
    }

    public function getTrPrio(): ?int
    {
        return $this->tr_prio;
    }

    public function setTrPrio(int $tr_prio): self
    {
        $this->tr_prio = $tr_prio;

        return $this;
    }

    public function getArtNr(): ?string
    {
        return $this->art_nr;
    }

    public function setArtNr(string $art_nr): self
    {
        $this->art_nr = $art_nr;

        return $this;
    }

    public function getTrQuantity(): ?string
    {
        return $this->tr_quantity;
    }

    public function setTrQuantity(string $tr_quantity): self
    {
        $this->tr_quantity = $tr_quantity;

        return $this;
    }

    public function getStockCoordinate(): ?string
    {
        return $this->stock_coordinate;
    }

    public function setStockCoordinate(string $stock_coordinate): self
    {
        $this->stock_coordinate = $stock_coordinate;

        return $this;
    }

    public function getStockNr(): ?int
    {
        return $this->stock_nr;
    }

    public function setStockNr(int $stock_nr): self
    {
        $this->stock_nr = $stock_nr;

        return $this;
    }

    public function getStockLevel1(): ?int
    {
        return $this->stock_level1;
    }

    public function setStockLevel1(int $stock_level1): self
    {
        $this->stock_level1 = $stock_level1;

        return $this;
    }

    public function getStockLevel2(): ?int
    {
        return $this->stock_level2;
    }

    public function setStockLevel2(int $stock_level2): self
    {
        $this->stock_level2 = $stock_level2;

        return $this;
    }

    public function getStockLevel3(): ?int
    {
        return $this->stock_level3;
    }

    public function setStockLevel3(int $stock_level3): self
    {
        $this->stock_level3 = $stock_level3;

        return $this;
    }

    public function getStockLevel4(): ?int
    {
        return $this->stock_level4;
    }

    public function setStockLevel4(int $stock_level4): self
    {
        $this->stock_level4 = $stock_level4;

        return $this;
    }

    public function getTrAccess(): ?\DateTimeInterface
    {
        return $this->tr_access;
    }

    public function setTrAccess(?\DateTimeInterface $tr_access): self
    {
        $this->tr_access = $tr_access;

        return $this;
    }

    public function getTrDispatch(): ?\DateTimeInterface
    {
        return $this->tr_dispatch;
    }

    public function setTrDispatch(?\DateTimeInterface $tr_dispatch): self
    {
        $this->tr_dispatch = $tr_dispatch;

        return $this;
    }

    public function getTrState(): ?int
    {
        return $this->tr_state;
    }

    public function setTrState(?int $tr_state): self
    {
        $this->tr_state = $tr_state;

        return $this;
    }

    public function getOrderUsername(): ?string
    {
        return $this->order_username;
    }

    public function setOrderUsername(string $order_username): self
    {
        $this->order_username = $order_username;

        return $this;
    }

    public function getBookingMethod(): ?string
    {
        return $this->booking_method;
    }

    public function setBookingMethod(string $booking_method): self
    {
        $this->booking_method = $booking_method;

        return $this;
    }

    public function getOrderNr(): ?string
    {
        return $this->order_nr;
    }

    public function setOrderNr(string $order_nr): self
    {
        $this->order_nr = $order_nr;

        return $this;
    }

    public function getOrderPos(): ?int
    {
        return $this->order_pos;
    }

    public function setOrderPos(int $order_pos): self
    {
        $this->order_pos = $order_pos;

        return $this;
    }

    public function getCharge(): ?string
    {
        return $this->charge;
    }

    public function setCharge(string $charge): self
    {
        $this->charge = $charge;

        return $this;
    }

    public function getLoadingEquipment(): ?string
    {
        return $this->loading_equipment;
    }

    public function setLoadingEquipment(string $loading_equipment): self
    {
        $this->loading_equipment = $loading_equipment;

        return $this;
    }

    public function getConfirmationState(): ?int
    {
        return $this->confirmation_state;
    }

    public function setConfirmationState(int $confirmation_state): self
    {
        $this->confirmation_state = $confirmation_state;

        return $this;
    }

    public function getTrUsername(): ?string
    {
        return $this->tr_username;
    }

    public function setTrUsername(string $tr_username): self
    {
        $this->tr_username = $tr_username;

        return $this;
    }

    public function getTrComputerIp(): ?string
    {
        return $this->tr_computer_ip;
    }

    public function setTrComputerIp(string $tr_computer_ip): self
    {
        $this->tr_computer_ip = $tr_computer_ip;

        return $this;
    }

    public function getTrBlocked(): ?int
    {
        return $this->tr_blocked;
    }

    public function setTrBlocked(int $tr_blocked): self
    {
        $this->tr_blocked = $tr_blocked;

        return $this;
    }

    public function getTrStartDate(): ?\DateTimeInterface
    {
        return $this->tr_start_date;
    }

    public function setTrStartDate(\DateTimeInterface $tr_start_date): self
    {
        $this->tr_start_date = $tr_start_date;

        return $this;
    }

    public function getTrEdited(): ?string
    {
        return $this->tr_edited;
    }

    public function setTrEdited(?string $tr_edited): self
    {
        $this->tr_edited = $tr_edited;

        return $this;
    }

    public function getTrTyp(): ?string
    {
        return $this->tr_typ;
    }

    public function setTrTyp(string $tr_typ): self
    {
        $this->tr_typ = $tr_typ;

        return $this;
    }
}