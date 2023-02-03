<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'stock_occupancy')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\StockOccupancyRepository')]
class StockOccupancy
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    /** One Stock Location has One Stock Occupancy. */
    #[ORM\OneToOne(targetEntity: StockLocation::class)]
    #[ORM\JoinColumn(name: 'stock_location_id', referencedColumnName: 'stock_location_id')]
    #[ORM\Column(name: 'stock_location_id', type: 'integer', nullable: false)]
    private int $stockLocationId;

    #[ORM\Column(name: 'article_id', type: 'integer', nullable: false)]
    private int $articleId;

    #[ORM\Column(name: 'in_stock', type: 'integer', nullable: false)]
    private int $inStock;

    #[ORM\Column(name: 'incoming_stock', type: 'integer', nullable: false)]
    private int $incomingStock;

    #[ORM\Column(name: 'reserved_stock', type: 'integer', nullable: false)]
    private int $reservedStock;

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

    public function getStockLocationId(): int
    {
        return $this->stockLocationId;
    }

    public function setStockLocationId(int $stockLocationId): self
    {
        $this->stockLocationId = $stockLocationId;

        return $this;
    }

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function setArticleId(int $articleId): self
    {
        $this->articleId = $articleId;

        return $this;
    }

    public function getInStock(): int
    {
        return $this->inStock;
    }

    public function setInStock(int $inStock): self
    {
        $this->inStock = $inStock;

        return $this;
    }

    public function getIncomingStock(): int
    {
        return $this->incomingStock;
    }

    public function setIncomingStock(int $incomingStock): self
    {
        $this->incomingStock = $incomingStock;

        return $this;
    }

    public function getReservedStock(): int
    {
        return $this->reservedStock;
    }

    public function setReservedStock(int $reservedStock): self
    {
        $this->reservedStock = $reservedStock;

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
