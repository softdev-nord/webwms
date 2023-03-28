<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package:    WebWMS\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockZoneLayout
 */
#[ORM\Table(name: 'stock_zone_layout')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\StockZoneLayoutRepository')]
class StockZoneLayout
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'stock_zone_short_desc', type: 'string', length: 100, nullable: false)]
    private string $stockZoneShortDesc;

    #[ORM\Column(name: 'from_coordinate', type: 'decimal', precision: 25, scale: 0, nullable: false)]
    private float $fromCoordinate;

    #[ORM\Column(name: 'from_stock_nr', type: 'integer', nullable: false)]
    private int $fromStockNr;

    #[ORM\Column(name: 'from_level1', type: 'integer', nullable: false)]
    private int $fromLevel1;

    #[ORM\Column(name: 'from_level2', type: 'integer', nullable: false)]
    private int $fromLevel2;

    #[ORM\Column(name: 'from_level3', type: 'integer', nullable: false)]
    private int $fromLevel3;

    #[ORM\Column(name: 'from_level4', type: 'integer', nullable: false)]
    private int $fromLevel4;

    #[ORM\Column(name: 'to_coordinate', type: 'decimal', precision: 25, scale: 0, nullable: false)]
    private float $toCoordinate;

    #[ORM\Column(name: 'to_level1', type: 'integer', nullable: false)]
    private int $toLevel1;

    #[ORM\Column(name: 'to_level2', type: 'integer', nullable: false)]
    private int $toLevel2;

    #[ORM\Column(name: 'to_level3', type: 'integer', nullable: false)]
    private int $toLevel3;

    #[ORM\Column(name: 'to_level4', type: 'integer', nullable: false)]
    private int $toLevel4;

    #[ORM\Column(name: 'sum_stock_loc', type: 'integer', nullable: false)]
    private int $sumStockLoc;

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

    public function getStockZoneShortDesc(): string
    {
        return $this->stockZoneShortDesc;
    }

    public function setZoneShortDesc(string $stockZoneShortDesc): self
    {
        $this->stockZoneShortDesc = $stockZoneShortDesc;

        return $this;
    }

    public function getFromCoordinate(): float
    {
        return $this->fromCoordinate;
    }

    public function setFromCoordinate(float $fromCoordinate): self
    {
        $this->fromCoordinate = $fromCoordinate;

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

    public function getFromLevel1(): int
    {
        return $this->fromLevel1;
    }

    public function setFromLevel1(int $fromLevel1): self
    {
        $this->fromLevel1 = $fromLevel1;

        return $this;
    }

    public function getFromLevel2(): int
    {
        return $this->fromLevel2;
    }

    public function setFromLevel2(int $fromLevel2): self
    {
        $this->fromLevel2 = $fromLevel2;

        return $this;
    }

    public function getFromLevel3(): int
    {
        return $this->fromLevel3;
    }

    public function setFromLevel3(int $fromLevel3): self
    {
        $this->fromLevel3 = $fromLevel3;

        return $this;
    }

    public function getFromLevel4(): int
    {
        return $this->fromLevel4;
    }

    public function setFromLevel4(int $fromLevel4): self
    {
        $this->fromLevel4 = $fromLevel4;

        return $this;
    }

    public function getToCoordinate(): float
    {
        return $this->toCoordinate;
    }

    public function setToCoordinate(float $toCoordinate): self
    {
        $this->toCoordinate = $toCoordinate;

        return $this;
    }

    public function getToLevel1(): int
    {
        return $this->toLevel1;
    }

    public function setToLevel1(int $toLevel1): self
    {
        $this->toLevel1 = $toLevel1;

        return $this;
    }

    public function getToLevel2(): int
    {
        return $this->toLevel2;
    }

    public function setToLevel2(int $toLevel2): self
    {
        $this->toLevel2 = $toLevel2;

        return $this;
    }

    public function getToLevel3(): int
    {
        return $this->toLevel3;
    }

    public function setToLevel3(int $toLevel3): self
    {
        $this->toLevel3 = $toLevel3;

        return $this;
    }

    public function getToLevel4(): int
    {
        return $this->toLevel4;
    }

    public function setToLevel4(int $toLevel4): self
    {
        $this->toLevel4 = $toLevel4;

        return $this;
    }

    public function getSumStockLoc(): int
    {
        return $this->sumStockLoc;
    }

    public function setSumStockLoc(int $sumStockLoc): self
    {
        $this->sumStockLoc = $sumStockLoc;

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
