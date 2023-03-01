<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'supplier_orders')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\SupplierOrderRepository')]
#[ApiResource(
    extraProperties: [
        'standard_put' => true,
    ],
)]
class SupplierOrder
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
    #[ORM\OneToMany(
        mappedBy: 'supplierOrder',
        targetEntity: SupplierOrderPos::class,
        cascade: ['persist'],
        fetch: 'EAGER'
    )]
    private Collection|ArrayCollection $supplierOrderPos;

    #[ORM\OneToOne(targetEntity: Supplier::class)]
    #[ORM\JoinColumn(name: 'supplier_id', referencedColumnName: 'supplier_id')]
    private ?Supplier $supplier;

    public function __construct()
    {
        $this->supplierOrderPos = new ArrayCollection();
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

    public function setUsrId(int $usrId): self
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

    public function getSupplierOrderPos(): Collection
    {
        return $this->supplierOrderPos;
    }

    public function addSupplierOrderPos(SupplierOrderPos $supplierOrderPos): self
    {
        if (!$this->supplierOrderPos->contains($supplierOrderPos)) {
            $this->supplierOrderPos->add($supplierOrderPos);
            $supplierOrderPos->setSupplierOrder($this);
        }

        return $this;
    }

    public function removeSupplierOrderPos(SupplierOrderPos $supplierOrderPos): self
    {
        if ($this->supplierOrderPos->removeElement($supplierOrderPos)) {
            // set the owning side to null (unless already changed)
            if ($supplierOrderPos->getSupplierOrder() === $this) {
                $supplierOrderPos->setSupplierOrder(null);
            }
        }

        return $this;
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }
}
