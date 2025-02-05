<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Repository\StockRotationRepository;

#[ClassInformation(
    package: 'WebWMS\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockRotation'
)]
#[ORM\Table(name: 'stock_rotation')]
#[ORM\Entity(repositoryClass: StockRotationRepository::class)]
class StockRotation
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(name: 'stock_location_id', type: Types::INTEGER, nullable: false)]
    private int $stockLocationId;

    #[ORM\Column(name: 'article_id', type: Types::INTEGER, nullable: false)]
    private int $articleId;

    #[ORM\Column(name: 'usr_id', type: Types::INTEGER, nullable: false)]
    private int $usrId;

    #[ORM\Column(name: 'customer_order_id', type: Types::INTEGER, nullable: true)]
    private ?int $customerOrderId = null;

    #[ORM\Column(name: 'supplier_order_id', type: Types::INTEGER, nullable: true)]
    private ?int $supplierOrderId = null;

    #[ORM\Column(name: 'movement_id', type: Types::INTEGER, nullable: false)]
    private int $movementId;

    #[ORM\Column(name: 'pos_quantity', type: Types::INTEGER, nullable: false)]
    private int $posQuantity;

    #[ORM\Column(name: 'access_date', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $accessDate = null;

    #[ORM\Column(name: 'dispatch_date', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $dispatchDate = null;

    #[ORM\Column(name: 'tr_type', type: Types::STRING, length: 8, nullable: false)]
    private string $trType;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $createdAt = null;

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

    public function getUsrId(): int
    {
        return $this->usrId;
    }

    public function setUsrId(int $usrId): self
    {
        $this->usrId = $usrId;

        return $this;
    }

    public function getCustomerOrderId(): ?int
    {
        return $this->customerOrderId;
    }

    public function setCustomerOrderId(int $customerOrderId): self
    {
        $this->customerOrderId = $customerOrderId;

        return $this;
    }

    public function getSupplierOrderId(): ?int
    {
        return $this->supplierOrderId;
    }

    public function setSupplierOrderId(int $supplierOrderId): self
    {
        $this->supplierOrderId = $supplierOrderId;

        return $this;
    }

    public function getMovementId(): int
    {
        return $this->movementId;
    }

    public function setMovementId(int $movementId): self
    {
        $this->movementId = $movementId;

        return $this;
    }

    public function getPosQuantity(): int
    {
        return $this->posQuantity;
    }

    public function setPosQuantity(int $posQuantity): self
    {
        $this->posQuantity = $posQuantity;

        return $this;
    }

    public function getAccessDate(): ?DateTimeInterface
    {
        return $this->accessDate;
    }

    public function setAccessDate(?DateTimeInterface $accessDate): self
    {
        $this->accessDate = $accessDate;

        return $this;
    }

    public function getDispatchDate(): ?DateTimeInterface
    {
        return $this->dispatchDate;
    }

    public function setDispatchDate(?DateTimeInterface $dispatchDate): self
    {
        $this->dispatchDate = $dispatchDate;

        return $this;
    }

    public function getTrType(): string
    {
        return $this->trType;
    }

    public function setTrType(string $trType): self
    {
        $this->trType = $trType;

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
}
