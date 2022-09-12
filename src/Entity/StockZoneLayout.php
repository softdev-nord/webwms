<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\StockZoneLayoutRepository;

/**
 * @ORM\Entity(repositoryClass=StockZoneLayoutRepository::class)
 */
class StockZoneLayout
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private ?string $zoneShortDesc;

    /**
     * @ORM\Column(type="decimal", precision=25, scale=0)
     */
    private ?string $fromCoordinate;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $fromStockNr;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $fromLevel1;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $fromLevel2;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $fromLevel3;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $fromLevel4;

    /**
     * @ORM\Column(type="decimal", precision=25, scale=0)
     */
    private ?string $toCoordinate;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $toLevel1;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $toLevel2;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $toLevel3;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $toLevel4;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $sumStockLoc;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getZoneShortDesc(): ?string
    {
        return $this->zoneShortDesc;
    }

    public function setZoneShortDesc(string $zoneShortDesc): self
    {
        $this->zoneShortDesc = $zoneShortDesc;

        return $this;
    }

    public function getFromCoordinate(): ?string
    {
        return $this->fromCoordinate;
    }

    public function setFromCoordinate(string $fromCoordinate): self
    {
        $this->fromCoordinate = $fromCoordinate;

        return $this;
    }

    public function getFromStockNr(): ?int
    {
        return $this->fromStockNr;
    }

    public function setFromStockNr(int $fromStockNr): self
    {
        $this->fromStockNr = $fromStockNr;

        return $this;
    }

    public function getFromLevel1(): ?int
    {
        return $this->fromLevel1;
    }

    public function setFromLevel1(int $fromLevel1): self
    {
        $this->fromLevel1 = $fromLevel1;

        return $this;
    }

    public function getFromLevel2(): ?int
    {
        return $this->fromLevel2;
    }

    public function setFromLevel2(int $fromLevel2): self
    {
        $this->fromLevel2 = $fromLevel2;

        return $this;
    }

    public function getFromLevel3(): ?int
    {
        return $this->fromLevel3;
    }

    public function setFromLevel3(int $fromLevel3): self
    {
        $this->fromLevel3 = $fromLevel3;

        return $this;
    }

    public function getFromLevel4(): ?int
    {
        return $this->fromLevel4;
    }

    public function setFromLevel4(int $fromLevel4): self
    {
        $this->fromLevel4 = $fromLevel4;

        return $this;
    }

    public function getToCoordinate(): ?string
    {
        return $this->toCoordinate;
    }

    public function setToCoordinate(string $toCoordinate): self
    {
        $this->toCoordinate = $toCoordinate;

        return $this;
    }

    public function getToLevel1(): ?int
    {
        return $this->toLevel1;
    }

    public function setToLevel1(int $toLevel1): self
    {
        $this->toLevel1 = $toLevel1;

        return $this;
    }

    public function getToLevel2(): ?int
    {
        return $this->toLevel2;
    }

    public function setToLevel2(int $toLevel2): self
    {
        $this->toLevel2 = $toLevel2;

        return $this;
    }

    public function getToLevel3(): ?int
    {
        return $this->toLevel3;
    }

    public function setToLevel3(int $toLevel3): self
    {
        $this->toLevel3 = $toLevel3;

        return $this;
    }

    public function getToLevel4(): ?int
    {
        return $this->toLevel4;
    }

    public function setToLevel4(int $toLevel4): self
    {
        $this->toLevel4 = $toLevel4;

        return $this;
    }

    public function getSumStockLoc(): ?int
    {
        return $this->sumStockLoc;
    }

    public function setSumStockLoc(int $sumStockLoc): self
    {
        $this->sumStockLoc = $sumStockLoc;

        return $this;
    }
}
