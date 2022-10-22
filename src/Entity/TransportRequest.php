<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\TransportRequestRepository;

/**
 * @ORM\Entity(repositoryClass=TransportRequestRepository::class)
 */
class TransportRequest
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $suId;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $trNr;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $trPos;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private ?int $trPrio;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private ?string $articleNr;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=3)
     */
    private ?string $trQuantity;

    /**
     * @ORM\Column(type="decimal", precision=25, scale=0)
     */
    private ?float $stockCoordinate;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stockNr;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stockLevel1;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stockLevel2;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stockLevel3;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stockLevel4;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"default": NULL})
     */
    private ?\DateTimeInterface $trAccess;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"default": NULL})
     */
    private ?\DateTimeInterface $trDispatch;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $trState;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private ?string $orderUsername;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private ?string $bookingMethod;

    /**
     * @ORM\Column(type="integer", length=11)
     */
    private ?int $docId;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private ?string $orderNr;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $orderPos;

    /**
     * @ORM\Column(type="string", length=20, options={"default": NULL})
     */
    private ?string $charge;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private ?string $loadingEquipment;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $confirmationState;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private ?string $trUsername;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private ?string $trComputerIp;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $trBlocked;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $trStartDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $trEdited;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private ?string $trTyp;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSuId(): ?int
    {
        return $this->suId;
    }

    public function setSuId(int $suId): self
    {
        $this->suId = $suId;

        return $this;
    }

    public function getTrNr(): ?int
    {
        return $this->trNr;
    }

    public function setTrNr(int $trNr): self
    {
        $this->trNr = $trNr;

        return $this;
    }

    public function getTrPos(): ?int
    {
        return $this->trPos;
    }

    public function setTrPos(int $trPos): self
    {
        $this->trPos = $trPos;

        return $this;
    }

    public function getTrPrio(): ?int
    {
        return $this->trPrio;
    }

    public function setTrPrio(int $trPrio): self
    {
        $this->trPrio = $trPrio;

        return $this;
    }

    public function getArtNr(): ?string
    {
        return $this->articleNr;
    }

    public function setArtNr(string $articleNr): self
    {
        $this->articleNr = $articleNr;

        return $this;
    }

    public function getTrQuantity(): ?string
    {
        return $this->trQuantity;
    }

    public function setTrQuantity(string $trQuantity): self
    {
        $this->trQuantity = $trQuantity;

        return $this;
    }

    public function getStockCoordinate(): ?float
    {
        return $this->stockCoordinate;
    }

    public function setStockCoordinate(?float $stockCoordinate): self
    {
        $this->stockCoordinate = $stockCoordinate;

        return $this;
    }

    public function getStockNr(): ?int
    {
        return $this->stockNr;
    }

    public function setStockNr(int $stockNr): self
    {
        $this->stockNr = $stockNr;

        return $this;
    }

    public function getStockLevel1(): ?int
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

    public function getStockLevel3(): ?int
    {
        return $this->stockLevel3;
    }

    public function setStockLevel3(int $stockLevel3): self
    {
        $this->stockLevel3 = $stockLevel3;

        return $this;
    }

    public function getStockLevel4(): ?int
    {
        return $this->stockLevel4;
    }

    public function setStockLevel4(int $stockLevel4): self
    {
        $this->stockLevel4 = $stockLevel4;

        return $this;
    }

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

    public function getTrState(): ?int
    {
        return $this->trState;
    }

    public function setTrState(?int $trState): self
    {
        $this->trState = $trState;

        return $this;
    }

    public function getOrderUsername(): ?string
    {
        return $this->orderUsername;
    }

    public function setOrderUsername(string $orderUsername): self
    {
        $this->orderUsername = $orderUsername;

        return $this;
    }

    public function getBookingMethod(): ?string
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

    public function setDocId($docId): self
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

    public function getLoadingEquipment(): ?string
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

    public function getTrComputerIp(): ?string
    {
        return $this->trComputerIp;
    }

    public function setTrComputerIp(string $trComputerIp): self
    {
        $this->trComputerIp = $trComputerIp;

        return $this;
    }

    public function getTrBlocked(): ?int
    {
        return $this->trBlocked;
    }

    public function setTrBlocked(?int $trBlocked): self
    {
        $this->trBlocked = $trBlocked;

        return $this;
    }

    public function getTrStartDate(): ?\DateTimeInterface
    {
        return $this->trStartDate;
    }

    public function setTrStartDate(?\DateTimeInterface $trStartDate): self
    {
        $this->trStartDate = $trStartDate;

        return $this;
    }

    public function getTrEdited(): ?string
    {
        return $this->trEdited;
    }

    public function setTrEdited(?string $trEdited): self
    {
        $this->trEdited = $trEdited;

        return $this;
    }

    public function getTrTyp(): ?string
    {
        return $this->trTyp;
    }

    public function setTrTyp(string $trTyp): self
    {
        $this->trTyp = $trTyp;

        return $this;
    }
}
