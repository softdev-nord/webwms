<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\StockLocationRepository;

/**
 * @ORM\Entity(repositoryClass=StockLocationRepository::class)
 */
class StockLocation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $stockLocationId;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stockLocationLn;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stockLocationFb;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stockLocationSp;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stockLocationTf;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private mixed $stockLocationCoordinate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $stockLocationDesc;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private ?string $stockLocationWidth;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private ?string $stockLocationDepth;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private ?string $stockLocationHeight;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private ?string $stockLocationZone;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $stockLocationCreatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $stockLocationUpdatedAt;

    public function getStockLocationId(): ?int
    {
        return $this->stockLocationId;
    }

    public function setStockLocationId(int $stockLocationId): self
    {
        $this->stockLocationId = $stockLocationId;

        return $this;
    }

    public function getStockLocationLn(): ?int
    {
        return $this->stockLocationLn;
    }

    public function setStockLocationLn(int $stockLocationLn): self
    {
        $this->stockLocationLn = $stockLocationLn;

        return $this;
    }

    public function getStockLocationFb(): ?int
    {
        return $this->stockLocationFb;
    }

    public function setStockLocationFb(int $stockLocationFb): self
    {
        $this->stockLocationFb = $stockLocationFb;

        return $this;
    }

    public function getStockLocationSp(): ?int
    {
        return $this->stockLocationSp;
    }

    public function setStockLocationSp(int $stockLocationSp): self
    {
        $this->stockLocationSp = $stockLocationSp;

        return $this;
    }

    public function getStockLocationTf(): ?int
    {
        return $this->stockLocationTf;
    }

    public function setStockLocationTf(int $stockLocationTf): self
    {
        $this->stockLocationTf = $stockLocationTf;

        return $this;
    }

    public function getStockLocationCoordinate(): mixed
    {
        return $this->stockLocationCoordinate;
    }

    public function setStockLocationCoordinate($stockLocationCoordinate): void
    {
        $this->stockLocationCoordinate = $stockLocationCoordinate;
    }

    public function getStockLocationDesc(): ?string
    {
        return $this->stockLocationDesc;
    }

    public function setStockLocationDesc(string $stockLocationDesc): self
    {
        $this->stockLocationDesc = $stockLocationDesc;

        return $this;
    }

    public function getStockLocationWidth(): ?string
    {
        return $this->stockLocationWidth;
    }

    public function setStockLocationWidth(string $stockLocationWidth): self
    {
        $this->stockLocationWidth = $stockLocationWidth;

        return $this;
    }

    public function getStockLocationDepth(): ?string
    {
        return $this->stockLocationDepth;
    }

    public function setStockLocationDepth(string $stockLocationDepth): self
    {
        $this->stockLocationDepth = $stockLocationDepth;

        return $this;
    }

    public function getStockLocationHeight(): ?string
    {
        return $this->stockLocationHeight;
    }

    public function setStockLocationHeight(string $stockLocationHeight): self
    {
        $this->stockLocationHeight = $stockLocationHeight;

        return $this;
    }

    public function getStockLocationZone(): ?string
    {
        return $this->stockLocationZone;
    }

    public function setStockLocationZone(?string $stockLocationZone): void
    {
        $this->stockLocationZone = $stockLocationZone;
    }

    public function getStockLocationCreatedAt(): mixed
    {
        return $this->stockLocationCreatedAt;
    }

    public function setStockLocationCreatedAt(mixed $stockLocationCreatedAt): void
    {
        $this->stockLocationCreatedAt = $stockLocationCreatedAt;
    }

    public function getStockLocationUpdatedAt(): mixed
    {
        return $this->stockLocationUpdatedAt;
    }

    public function setStockLocationUpdatedAt(mixed $stockLocationUpdatedAt): void
    {
        $this->stockLocationUpdatedAt = $stockLocationUpdatedAt;
    }
}
