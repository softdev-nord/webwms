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
    private ?int $stock_location_id;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stock_location_ln;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stock_location_fb;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stock_location_sp;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stock_location_tf;

    /**
     * @ORM\Column(type="integer")
     * * @ORM\OneToMany(targetEntity="\WebWMS\Entity\StockRotation", mappedBy="stock_location_coordinate")
     */
    private mixed $stock_location_coordinate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $stock_location_desc;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private ?string $stock_location_width;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private ?string $stock_location_depth;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $stock_location_created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $stock_location_updated_at;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private ?string $stock_location_height;

    public function getStockLocationId(): ?int
    {
        return $this->stock_location_id;
    }

    public function setStockLocationId(int $stock_location_id): self
    {
        $this->stock_location_id = $stock_location_id;

        return $this;
    }

    public function getStockLocationLn(): ?int
    {
        return $this->stock_location_ln;
    }

    public function setStockLocationLn(int $stock_location_ln): self
    {
        $this->stock_location_ln = $stock_location_ln;

        return $this;
    }

    public function getStockLocationFb(): ?int
    {
        return $this->stock_location_fb;
    }

    public function setStockLocationFb(int $stock_location_fb): self
    {
        $this->stock_location_fb = $stock_location_fb;

        return $this;
    }

    public function getStockLocationSp(): ?int
    {
        return $this->stock_location_sp;
    }

    public function setStockLocationSp(int $stock_location_sp): self
    {
        $this->stock_location_sp = $stock_location_sp;

        return $this;
    }

    public function getStockLocationTf(): ?int
    {
        return $this->stock_location_tf;
    }

    public function setStockLocationTf(int $stock_location_tf): self
    {
        $this->stock_location_tf = $stock_location_tf;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStockLocationCoordinate(): mixed
    {
        return $this->stock_location_coordinate;
    }

    /**
     * @param mixed $stock_location_coordinate
     */
    public function setStockLocationCoordinate($stock_location_coordinate): void
    {
        $this->stock_location_coordinate = $stock_location_coordinate;
    }

    public function getStockLocationDesc(): ?string
    {
        return $this->stock_location_desc;
    }

    public function setStockLocationDesc(string $stock_location_desc): self
    {
        $this->stock_location_desc = $stock_location_desc;

        return $this;
    }

    public function getStockLocationWidth(): ?string
    {
        return $this->stock_location_width;
    }

    public function setStockLocationWidth(string $stock_location_width): self
    {
        $this->stock_location_width = $stock_location_width;

        return $this;
    }

    public function getStockLocationDepth(): ?string
    {
        return $this->stock_location_depth;
    }

    public function setStockLocationDepth(string $stock_location_depth): self
    {
        $this->stock_location_depth = $stock_location_depth;

        return $this;
    }

    public function getStockLocationHeight(): ?string
    {
        return $this->stock_location_height;
    }

    public function setStockLocationHeight(string $stock_location_height): self
    {
        $this->stock_location_height = $stock_location_height;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStockLocationCreatedAt(): mixed
    {
        return $this->stock_location_created_at;
    }

    /**
     * @param mixed $stock_location_created_at
     */
    public function setStockLocationCreatedAt(mixed $stock_location_created_at): void
    {
        $this->stock_location_created_at = $stock_location_created_at;
    }

    /**
     * @return mixed
     */
    public function getStockLocationUpdatedAt(): mixed
    {
        return $this->stock_location_updated_at;
    }

    /**
     * @param mixed $stock_location_updated_at
     */
    public function setStockLocationUpdatedAt(mixed $stock_location_updated_at): void
    {
        $this->stock_location_updated_at = $stock_location_updated_at;
    }
}
