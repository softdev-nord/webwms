<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Repository\StockZoneRepository;

#[ClassInformation(
    package: 'WebWMS\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockZone'
)]
#[ORM\Table(name: 'stock_zone')]
#[ORM\Entity(repositoryClass: StockZoneRepository::class)]
class StockZone
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(name: 'stock_zone_short_desc', type: Types::STRING, length: 100, nullable: false)]
    private string $stockZoneShortDesc;

    #[ORM\Column(name: 'stock_zone_description', type: Types::STRING, length: 255, nullable: false)]
    private string $stockZoneDescription;

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
