<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\StockOccupancyRepository;

/**
 * @package:    WebWMS\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockOccupancyEntity
 */
#[ORM\Table(name: 'stock_occupancy')]
#[ORM\Entity(repositoryClass: StockOccupancyRepository::class)]
#[ORM\Index(columns: ['stock_location_id'], name: 'stock_location_id_idx')]
#[ORM\Index(columns: ['article_id'], name: 'article_id_idx')]
class StockOccupancyEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    /** One Stock Location has One Stock Occupancy. */
    #[ORM\OneToOne(targetEntity: StockLocationEntity::class)]
    #[ORM\JoinColumn(name: 'stock_location_id', referencedColumnName: 'stock_location_id')]
    #[ORM\Column(name: 'stock_location_id', type: Types::INTEGER, nullable: false)]
    private int $stockLocationId;

    #[ORM\Column(name: 'stock_coordinate', type: Types::STRING, length: 25, nullable: false)]
    private string $stockCoordinate;

    #[ORM\Column(name: 'article_id', type: Types::INTEGER, nullable: true, options: ['default' => null])]
    private ?int $articleId;

    #[ORM\Column(name: 'in_stock', type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ['default' => null])]
    private ?float $inStock;

    #[ORM\Column(name: 'incoming_stock', type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ['default' => null])]
    private ?float $incomingStock;

    #[ORM\Column(name: 'reserved_stock', type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ['default' => null])]
    private ?float $reservedStock;

    #[ORM\Column(name: 'last_incoming', type: Types::DATETIME_MUTABLE, nullable: true, options: ['default' => null])]
    private ?DateTimeInterface $lastIncoming = null;

    #[ORM\Column(name: 'last_outgoing', type: Types::DATETIME_MUTABLE, nullable: true, options: ['default' => null])]
    private ?DateTimeInterface $lastOutgoing = null;

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

    public function getStockLocationId(): int
    {
        return $this->stockLocationId;
    }

    public function setStockLocationId(int $stockLocationId): self
    {
        $this->stockLocationId = $stockLocationId;

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

    public function getArticleId(): ?int
    {
        return $this->articleId;
    }

    public function setArticleId(?int $articleId): self
    {
        $this->articleId = $articleId;

        return $this;
    }

    public function getInStock(): ?float
    {
        return $this->inStock;
    }

    public function setInStock(?float $inStock): self
    {
        $this->inStock = $inStock;

        return $this;
    }

    public function getIncomingStock(): ?float
    {
        return $this->incomingStock;
    }

    public function setIncomingStock(?float $incomingStock): self
    {
        $this->incomingStock = $incomingStock;

        return $this;
    }

    public function getReservedStock(): ?float
    {
        return $this->reservedStock;
    }

    public function setReservedStock(?float $reservedStock): self
    {
        $this->reservedStock = $reservedStock;

        return $this;
    }

    public function getLastIncoming(): ?DateTimeInterface
    {
        return $this->lastIncoming;
    }

    public function setLastIncoming(?DateTimeInterface $lastIncoming): void
    {
        $this->lastIncoming = $lastIncoming;
    }

    public function getLastOutgoing(): ?DateTimeInterface
    {
        return $this->lastOutgoing;
    }

    public function setLastOutgoing(?DateTimeInterface $lastOutgoing): void
    {
        $this->lastOutgoing = $lastOutgoing;
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
