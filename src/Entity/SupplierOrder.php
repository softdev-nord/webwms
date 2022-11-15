<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Components\Entity\ModelEntity;

#[ORM\Table(name: 'supplier_orders')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\SupplierOrderRepository')]
class SupplierOrder extends ModelEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'supplier_order_id', type: 'integer', nullable: false)]
    private int $supplierOrderId;

    #[ORM\Column(name: 'usr_id', type: 'integer', nullable: false)]
    private int $usrId;

    #[ORM\Column(name: 'supplier_id', type: 'integer', nullable: false)]
    private int $supplierId;

    #[ORM\Column(name: 'supplier_order_nr', type: 'string', length: 255, nullable: false)]
    private string $supplierOrderNr;

    #[ORM\Column(name: 'supplier_order_reference', type: 'string', length: 255, nullable: true)]
    private ?string $supplierOrderReference;

    #[ORM\Column(name: 'supplier_order_date', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $supplierOrderDate;

    #[ORM\Column(name: 'supplier_order_creation_date', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $supplierOrderCreationDate;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt;

    /** One Supplier Order has many Supplier Order Positions. This is the inverse side. */
    #[ORM\OneToMany(mappedBy: 'supplierOrders', targetEntity: SupplierOrderPos::class)]
    protected Collection $details;

    /** Many Supplier Orders have one Supplier. This is the owning side. */
    #[ORM\ManyToOne(targetEntity: Supplier::class, inversedBy: 'supplierOrders')]
    #[ORM\JoinColumn(name: 'supplier_id', referencedColumnName: 'id')]
    protected Supplier $supplier;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getSupplierOrderId(): int
    {
        return $this->supplierOrderId;
    }

    public function setSupplierOrderId(int $supplierOrderId): self
    {
        $this->supplierOrderId = $supplierOrderId;

        return $this;
    }

    public function getUsrId(): int
    {
        return $this->usrId;
    }

    public function setUsrId(?int $usrId): self
    {
        $this->usrId = $usrId;

        return $this;
    }

    public function getSupplierId(): int
    {
        return $this->supplierId;
    }

    public function setSupplierId(int $supplierId): self
    {
        $this->supplierId = $supplierId;

        return $this;
    }

    public function getSupplierOrderNr(): string
    {
        return $this->supplierOrderNr;
    }

    public function setSupplierOrderNr(string $supplierOrderNr): self
    {
        $this->supplierOrderNr = $supplierOrderNr;

        return $this;
    }

    public function getSupplierOrderReference(): ?string
    {
        return $this->supplierOrderReference;
    }

    public function setSupplierOrderReference(?string $supplierOrderReference): self
    {
        $this->supplierOrderReference = $supplierOrderReference;

        return $this;
    }

    public function getSupplierOrderDate(): ?\DateTimeInterface
    {
        return $this->supplierOrderDate;
    }

    public function setSupplierOrderDate(?\DateTimeInterface $supplierOrderDate): self
    {
        $this->supplierOrderDate = $supplierOrderDate;

        return $this;
    }

    public function getSupplierOrderCreationDate(): ?\DateTimeInterface
    {
        return $this->supplierOrderCreationDate;
    }

    public function setSupplierOrderCreationDate(?\DateTimeInterface $supplierOrderCreationDate): self
    {
        $this->supplierOrderCreationDate = $supplierOrderCreationDate;

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

    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function setDetails(?array $details): SupplierOrder
    {
        return $this->setOneToMany($details, SupplierOrderPos::class, 'details', 'SupplierOrder');
    }

    public function getSupplier(): Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(Supplier $supplier)
    {
        $this->supplier = $supplier;
    }
}
