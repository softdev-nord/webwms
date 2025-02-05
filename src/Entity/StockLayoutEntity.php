<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Repository\StockLayoutRepository;

#[ClassInformation(
    package: 'WebWMS\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockLayout'
)]
#[ORM\Table(name: 'stock_layout')]
#[ORM\Entity(repositoryClass: StockLayoutRepository::class)]
class StockLayout
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(name: 'stock_nr', type: Types::INTEGER, nullable: false)]
    private int $stockNr;

    #[ORM\Column(name: 'stock_description', type: Types::STRING, length: 25, nullable: false)]
    private string $stockDescription;

    #[ORM\Column(name: 'stock_level1', type: Types::INTEGER, nullable: false)]
    private int $stockLevel1;

    #[ORM\Column(name: 'stock_level2', type: Types::INTEGER, nullable: false)]
    private int $stockLevel2;

    #[ORM\Column(name: 'stock_level3', type: Types::INTEGER, nullable: false)]
    private int $stockLevel3;

    #[ORM\Column(name: 'stock_level4', type: Types::INTEGER, nullable: false)]
    private int $stockLevel4;

    #[ORM\Column(name: 'stock_model', type: Types::STRING, length: 15, nullable: false)]
    private string $stockModel;

    #[ORM\Column(name: 'stock_typ', type: Types::STRING, length: 15, nullable: false)]
    private string $stockTyp;

    #[ORM\Column(name: 'stock_long_description', type: Types::STRING, length: 255, nullable: true)]
    private ?string $stockLongDescription = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getStockNr(): int
    {
        return $this->stockNr;
    }

    public function setStockNr(int $stockNr): self
    {
        $this->stockNr = $stockNr;

        return $this;
    }

    public function getStockDescription(): string
    {
        return $this->stockDescription;
    }

    public function setStockDescription(string $stockDescription): self
    {
        $this->stockDescription = $stockDescription;

        return $this;
    }

    public function getStockLevel1(): int
    {
        return $this->stockLevel1;
    }

    public function setStockLevel1(int $stockLevel1): self
    {
        $this->stockLevel1 = $stockLevel1;

        return $this;
    }

    public function getStockLevel2(): int
    {
        return $this->stockLevel2;
    }

    public function setStockLevel2(int $stockLevel2): self
    {
        $this->stockLevel2 = $stockLevel2;

        return $this;
    }

    public function getStockLevel3(): int
    {
        return $this->stockLevel3;
    }

    public function setStockLevel3(int $stockLevel3): self
    {
        $this->stockLevel3 = $stockLevel3;

        return $this;
    }

    public function getStockLevel4(): int
    {
        return $this->stockLevel4;
    }

    public function setStockLevel4(int $stockLevel4): self
    {
        $this->stockLevel4 = $stockLevel4;

        return $this;
    }

    public function getStockModel(): string
    {
        return $this->stockModel;
    }

    public function setStockModel(string $stockModel): self
    {
        $this->stockModel = $stockModel;

        return $this;
    }

    public function getStockTyp(): string
    {
        return $this->stockTyp;
    }

    public function setStockTyp(string $stockTyp): self
    {
        $this->stockTyp = $stockTyp;

        return $this;
    }

    public function getStockLongDescription(): ?string
    {
        return $this->stockLongDescription;
    }

    public function setStockLongDescription(string $stockLongDescription): self
    {
        $this->stockLongDescription = $stockLongDescription;

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
