<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\StockLocationRepository;

/**
 * @package:    WebWMS\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLocationEntity
 */
#[ORM\Table(name: 'stock_location')]
#[ORM\Entity(repositoryClass: StockLocationRepository::class)]
class StockLocationEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'stock_location_id', type: Types::INTEGER)]
    private int $stockLocationId;

    #[ORM\Column(name: 'stock_location_ln', type: Types::INTEGER, nullable: false)]
    private int $stockLocationLn;

    #[ORM\Column(name: 'stock_location_fb', type: Types::INTEGER, nullable: false)]
    private int $stockLocationFb;

    #[ORM\Column(name: 'stock_location_sp', type: Types::INTEGER, nullable: false)]
    private int $stockLocationSp;

    #[ORM\Column(name: 'stock_location_tf', type: Types::INTEGER, nullable: false)]
    private int $stockLocationTf;

    #[ORM\Column(name: 'stock_location_coordinate', type: Types::STRING, length: 25, nullable: false)]
    private string $stockLocationCoordinate;

    #[ORM\Column(name: 'stock_location_desc', type: Types::STRING, length: 255, nullable: false)]
    private string $stockLocationDesc;

    #[ORM\Column(name: 'stock_location_width', type: Types::DECIMAL, precision: 6, scale: 2, nullable: false)]
    private float $stockLocationWidth;

    #[ORM\Column(name: 'stock_location_depth', type: Types::DECIMAL, precision: 6, scale: 2, nullable: false)]
    private float $stockLocationDepth;

    #[ORM\Column(name: 'stock_location_height', type: Types::DECIMAL, precision: 6, scale: 2, nullable: false)]
    private float $stockLocationHeight;

    #[ORM\Column(name: 'stock_location_zone', type: Types::STRING, length: 10, nullable: false)]
    private string $stockLocationZone;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $updatedAt = null;

    public function getStockLocationId(): int
    {
        return $this->stockLocationId;
    }

    public function setStockLocationId(int $stockLocationId): self
    {
        $this->stockLocationId = $stockLocationId;

        return $this;
    }

    public function getStockLocationLn(): int
    {
        return $this->stockLocationLn;
    }

    public function setStockLocationLn(int $stockLocationLn): self
    {
        $this->stockLocationLn = $stockLocationLn;

        return $this;
    }

    public function getStockLocationFb(): int
    {
        return $this->stockLocationFb;
    }

    public function setStockLocationFb(int $stockLocationFb): self
    {
        $this->stockLocationFb = $stockLocationFb;

        return $this;
    }

    public function getStockLocationSp(): int
    {
        return $this->stockLocationSp;
    }

    public function setStockLocationSp(int $stockLocationSp): self
    {
        $this->stockLocationSp = $stockLocationSp;

        return $this;
    }

    public function getStockLocationTf(): int
    {
        return $this->stockLocationTf;
    }

    public function setStockLocationTf(int $stockLocationTf): self
    {
        $this->stockLocationTf = $stockLocationTf;

        return $this;
    }

    public function getStockLocationCoordinate(): string
    {
        return $this->stockLocationCoordinate;
    }

    public function setStockLocationCoordinate(string $stockLocationCoordinate): void
    {
        $this->stockLocationCoordinate = $stockLocationCoordinate;
    }

    public function getStockLocationDesc(): string
    {
        return $this->stockLocationDesc;
    }

    public function setStockLocationDesc(string $stockLocationDesc): self
    {
        $this->stockLocationDesc = $stockLocationDesc;

        return $this;
    }

    public function getStockLocationWidth(): float
    {
        return $this->stockLocationWidth;
    }

    public function setStockLocationWidth(float $stockLocationWidth): self
    {
        $this->stockLocationWidth = $stockLocationWidth;

        return $this;
    }

    public function getStockLocationDepth(): float
    {
        return $this->stockLocationDepth;
    }

    public function setStockLocationDepth(float $stockLocationDepth): self
    {
        $this->stockLocationDepth = $stockLocationDepth;

        return $this;
    }

    public function getStockLocationHeight(): float
    {
        return $this->stockLocationHeight;
    }

    public function setStockLocationHeight(float $stockLocationHeight): self
    {
        $this->stockLocationHeight = $stockLocationHeight;

        return $this;
    }

    public function getStockLocationZone(): string
    {
        return $this->stockLocationZone;
    }

    public function setStockLocationZone(string $stockLocationZone): void
    {
        $this->stockLocationZone = $stockLocationZone;
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
