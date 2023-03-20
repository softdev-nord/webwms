<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'stock_zone')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\StockZoneRepository')]
class StockZone
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'stock_zone_short_desc', type: 'string', length: 100, nullable: false)]
    private string $stockZoneShortDesc;

    #[ORM\Column(name: 'stock_zone_description', type: 'string', length: 255, nullable: false)]
    private string $stockZoneDescription;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt;

    public function __toString()
    {
        return $this->stockZoneShortDesc;
    }

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

    public function setStockZoneShortDesc(string $stockZoneShortDesc): self
    {
        $this->stockZoneShortDesc = $stockZoneShortDesc;

        return $this;
    }

    public function getStockZoneDescription(): ?string
    {
        return $this->stockZoneDescription;
    }

    public function setStockZoneDescription(string $stockZoneDescription): self
    {
        $this->stockZoneDescription = $stockZoneDescription;

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
