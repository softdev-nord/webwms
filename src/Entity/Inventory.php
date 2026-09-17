<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Repository\InventoryRepository;

#[ClassInformation(
    package: 'WebWMS\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'Inventory'
)]
#[ORM\Table(name: 'inventory')]
#[ORM\Entity(repositoryClass: InventoryRepository::class)]
class Inventory
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(name: 'inventory_nr', type: Types::STRING, length: 20, nullable: false, unique: true)]
    private string $inventoryNr;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 20, nullable: false, options: ['default' => 'open'])]
    private string $status = 'open';

    #[ORM\Column(name: 'scope', type: Types::STRING, length: 50, nullable: true)]
    private ?string $scope = null;

    #[ORM\Column(name: 'start_date', type: Types::DATETIME_MUTABLE, nullable: false)]
    private DateTimeInterface $startDate;

    #[ORM\Column(name: 'end_date', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $endDate = null;

    #[ORM\Column(name: 'started_by', type: Types::STRING, length: 30, nullable: false)]
    private string $startedBy;

    #[ORM\Column(name: 'completed_by', type: Types::STRING, length: 30, nullable: true)]
    private ?string $completedBy = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: false)]
    private DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $updatedAt = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getInventoryNr(): string
    {
        return $this->inventoryNr;
    }

    public function setInventoryNr(string $inventoryNr): self
    {
        $this->inventoryNr = $inventoryNr;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setScope(?string $scope): self
    {
        $this->scope = $scope;
        return $this;
    }

    public function getStartDate(): DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getStartedBy(): string
    {
        return $this->startedBy;
    }

    public function setStartedBy(string $startedBy): self
    {
        $this->startedBy = $startedBy;
        return $this;
    }

    public function getCompletedBy(): ?string
    {
        return $this->completedBy;
    }

    public function setCompletedBy(?string $completedBy): self
    {
        $this->completedBy = $completedBy;
        return $this;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
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

