<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Components\Entity\ModelEntity;
use WebWMS\Repository\SupplierOrderRepository;

/**
 * @ORM\Entity(repositoryClass=SupplierOrderRepository::class)
 * @ORM\Table(name="`supplier_orders`")
 */
class SupplierOrder extends ModelEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $supplierOrderId;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $usrId;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $supplierId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $supplierOrderNr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $supplierOrderReference;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $supplierOrderDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $supplierOrderOrderDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $supplierOrderCreatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $supplierOrderUpdatedAt;

    /**
     * INVERSE SIDE.
     *
     * @ORM\OneToMany(targetEntity="\WebWMS\Entity\SupplierOrderPos", mappedBy="supplierOrders", orphanRemoval=true, cascade={"persist"})
     */
    protected Collection $details;

    /**
     * @ORM\OneToOne(targetEntity="\WebWMS\Entity\Supplier")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    protected Supplier $supplier;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(mixed $id): void
    {
        $this->id = $id;
    }

    public function getSupplierOrderId(): ?int
    {
        return $this->supplierOrderId;
    }

    public function setSupplierOrderId(?int $supplierOrderId): void
    {
        $this->supplierOrderId = $supplierOrderId;
    }

    public function getUsrId(): ?int
    {
        return $this->usrId;
    }

    public function setUsrId(?int $usrId): void
    {
        $this->usrId = $usrId;
    }

    public function getSupplierId(): ?int
    {
        return $this->supplierId;
    }

    public function setSupplierId(?int $supplierId): void
    {
        $this->supplierId = $supplierId;
    }

    public function getSupplierOrderNr(): ?string
    {
        return $this->supplierOrderNr;
    }

    public function setSupplierOrderNr(?string $supplierOrderNr): void
    {
        $this->supplierOrderNr = $supplierOrderNr;
    }

    public function getSupplierOrderReference(): ?string
    {
        return $this->supplierOrderReference;
    }

    public function setSupplierOrderReference(?string $supplierOrderReference): void
    {
        $this->supplierOrderReference = $supplierOrderReference;
    }

    public function getSupplierOrderDate(): ?\DateTimeInterface
    {
        return $this->supplierOrderDate;
    }

    public function setSupplierOrderDate(?\DateTimeInterface $supplierOrderDate): void
    {
        $this->supplierOrderDate = $supplierOrderDate;
    }

    public function getSupplierOrderOrderDate(): ?\DateTimeInterface
    {
        return $this->supplierOrderOrderDate;
    }

    public function setSupplierOrderOrderDate(?\DateTimeInterface $supplierOrderOrderDate): void
    {
        $this->supplierOrderOrderDate = $supplierOrderOrderDate;
    }

    public function getSupplierOrderCreatedAt(): ?\DateTimeInterface
    {
        return $this->supplierOrderCreatedAt;
    }

    public function setSupplierOrderCreatedAt(?\DateTimeInterface $supplierOrderCreatedAt): void
    {
        $this->supplierOrderCreatedAt = $supplierOrderCreatedAt;
    }

    public function getSupplierOrderUpdatedAt(): ?\DateTimeInterface
    {
        return $this->supplierOrderUpdatedAt;
    }

    public function setSupplierOrderUpdatedAt(?\DateTimeInterface $supplierOrderUpdatedAt): void
    {
        $this->supplierOrderUpdatedAt = $supplierOrderUpdatedAt;
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
