<?php

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\StockOccupancyRepository;

#[ORM\Entity(repositoryClass: StockOccupancyRepository::class)]
class StockOccupancy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'stockLocationId', type: 'integer', nullable: false)]
    private ?int $stockLocationId = null;

    #[ORM\Column(name: 'articleId', type: 'integer', nullable: false)]
    private ?int $articleId = null;

    #[ORM\Column(name: 'stock', type: 'integer', nullable: false)]
    private ?int $stock = null;

    #[ORM\Column(name: 'incomingStock', type: 'integer', nullable: false)]
    private ?int $incomingStock = null;

    #[ORM\Column(name: 'reservedStock', type: 'integer', nullable: false)]
    private ?int $reservedStock = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStockLocationId(): ?int
    {
        return $this->stockLocationId;
    }

    public function setStockLocationId(int $stockLocationId): self
    {
        $this->stockLocationId = $stockLocationId;

        return $this;
    }

    public function getArticleId(): ?int
    {
        return $this->articleId;
    }

    public function setArticleId(int $articleId): self
    {
        $this->articleId = $articleId;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getIncomingStock(): ?int
    {
        return $this->incomingStock;
    }

    public function setIncomingStock(int $incomingStock): self
    {
        $this->incomingStock = $incomingStock;

        return $this;
    }

    public function getReservedStock(): ?int
    {
        return $this->reservedStock;
    }

    public function setReservedStock(int $reservedStock): self
    {
        $this->reservedStock = $reservedStock;

        return $this;
    }
}
