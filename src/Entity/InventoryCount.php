<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Repository\InventoryCountRepository;

#[ClassInformation(
    package: 'WebWMS\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'InventoryCount'
)]
#[ORM\Table(name: 'inventory_count')]
#[ORM\Entity(repositoryClass: InventoryCountRepository::class)]
class InventoryCount
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Inventory::class)]
    #[ORM\JoinColumn(name: 'inventory_id', referencedColumnName: 'id', nullable: false)]
    private Inventory $inventory;

    #[ORM\Column(name: 'stock_location_id', type: Types::INTEGER, nullable: false)]
    private int $stockLocationId;

    #[ORM\Column(name: 'article_id', type: Types::INTEGER, nullable: false)]
    private int $articleId;

    #[ORM\Column(name: 'article_nr', type: Types::STRING, length: 20, nullable: false)]
    private string $articleNr;

    #[ORM\Column(name: 'expected_quantity', type: Types::DECIMAL, precision: 11, scale: 3, nullable: false)]
    private float $expectedQuantity;

    #[ORM\Column(name: 'counted_quantity', type: Types::DECIMAL, precision: 11, scale: 3, nullable: true)]
    private ?float $countedQuantity = null;

    #[ORM\Column(name: 'difference', type: Types::DECIMAL, precision: 11, scale: 3, nullable: true)]
    private ?float $difference = null;

    #[ORM\Column(name: 'counted_by', type: Types::STRING, length: 30, nullable: true)]
    private ?string $countedBy = null;

    #[ORM\Column(name: 'counted_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $countedAt = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: false)]
    private DateTimeInterface $createdAt;

    // Getters/Setters
    public function getId(): int { return $this->id; }
    public function getInventory(): Inventory { return $this->inventory; }
    public function setInventory(Inventory $inventory): self { $this->inventory = $inventory; return $this; }
    public function getStockLocationId(): int { return $this->stockLocationId; }
    public function setStockLocationId(int $id): self { $this->stockLocationId = $id; return $this; }
    public function getArticleId(): int { return $this->articleId; }
    public function setArticleId(int $id): self { $this->articleId = $id; return $this; }
    public function getArticleNr(): string { return $this->articleNr; }
    public function setArticleNr(string $nr): self { $this->articleNr = $nr; return $this; }
    public function getExpectedQuantity(): float { return $this->expectedQuantity; }
    public function setExpectedQuantity(float $qty): self { $this->expectedQuantity = $qty; return $this; }
    public function getCountedQuantity(): ?float { return $this->countedQuantity; }
    public function setCountedQuantity(?float $qty): self { $this->countedQuantity = $qty; return $this; }
    public function getDifference(): ?float { return $this->difference; }
    public function setDifference(?float $diff): self { $this->difference = $diff; return $this; }
    public function getCountedBy(): ?string { return $this->countedBy; }
    public function setCountedBy(?string $user): self { $this->countedBy = $user; return $this; }
    public function getCountedAt(): ?DateTimeInterface { return $this->countedAt; }
    public function setCountedAt(?DateTimeInterface $dt): self { $this->countedAt = $dt; return $this; }
    public function getCreatedAt(): DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(DateTimeInterface $dt): self { $this->createdAt = $dt; return $this; }
}

