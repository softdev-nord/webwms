<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Repository\SupplierOrderRepository;

#[ClassInformation(
    package: 'WebWMS\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'SupplierOrder'
)]
#[ORM\Table(name: 'supplier_orders')]
#[ORM\Entity(repositoryClass: SupplierOrderRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'skip_null_values' => false,
                'groups' => ['supplierOrder:read', 'supplierOrderPos:read', 'supplier:read'],
            ]
        ),
        new GetCollection(
            normalizationContext: [
                'skip_null_values' => false,
                'groups' => ['supplierOrder:read', 'supplierOrderPos:read', 'supplier:read'],
            ]
        ),
        new Post(
            denormalizationContext: [
                'groups' => ['supplierOrder:write', 'supplierOrderPos:write', 'supplier:write'],
            ]
        ),
        new Put(
            denormalizationContext: [
                'groups' => ['supplierOrder:write', 'supplierOrderPos:write', 'supplier:write'],
            ]
        ),
        new Patch(
            denormalizationContext: [
                'groups' => ['supplierOrder:write', 'supplierOrderPos:write', 'supplier:write'],
            ]
        ),
        new Delete(
            denormalizationContext: [
                'groups' => ['supplierOrder:write', 'supplierOrderPos:write', 'supplier:write'],
            ]
        ),
    ],
    formats: ['json'],
    normalizationContext: ['groups' => ['supplierOrder:read']],
    denormalizationContext: ['groups' => ['supplierOrder:write']]
)]
class SupplierOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(name: 'supplier_order_id', type: Types::INTEGER, nullable: false)]
    #[Groups(['supplierOrder:read', 'supplierOrder:write'])]
    private int $supplierOrderId;

    #[ORM\Column(name: 'usr_id', type: Types::INTEGER, nullable: false)]
    #[Groups(['supplierOrder:read', 'supplierOrder:write'])]
    private int $usrId;

    #[ORM\Column(name: 'supplier_id', type: Types::INTEGER, nullable: false)]
    #[Groups(['supplierOrder:read', 'supplierOrder:write'])]
    private int $supplierId;

    #[ORM\Column(name: 'supplier_order_nr', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['supplierOrder:read', 'supplierOrder:write'])]
    private string $supplierOrderNr;

    #[ORM\Column(name: 'supplier_order_reference', type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['supplierOrder:read', 'supplierOrder:write'])]
    private ?string $supplierOrderReference = null;

    #[ORM\Column(name: 'supplier_order_date', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['supplierOrder:read', 'supplierOrder:write'])]
    private ?DateTimeInterface $supplierOrderDate = null;

    #[ORM\Column(name: 'supplier_order_creation_date', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['supplierOrder:read', 'supplierOrder:write'])]
    private ?DateTimeInterface $supplierOrderCreationDate = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['supplierOrder:read', 'supplierOrder:write'])]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['supplierOrder:read', 'supplierOrder:write'])]
    private ?DateTimeInterface $updatedAt = null;

    /** One Supplier Order has many Supplier Order Positions. This is the inverse side.
     * @var Collection<int, SupplierOrderPos> */
    #[ORM\OneToMany(
        mappedBy: 'supplierOrder',
        targetEntity: SupplierOrderPos::class,
        cascade: ['persist'],
        fetch: 'EAGER',
        orphanRemoval: true
    )]
    #[Groups(['supplierOrder:read'])]
    private Collection $supplierOrderPos;

    /** Many Supplier Orders has one Supplier. This is the owning side. */
    #[ORM\ManyToOne(targetEntity: Supplier::class, inversedBy: 'supplierOrder')]
    #[ORM\JoinColumn(name: 'supplier_id', referencedColumnName: 'supplier_id')]
    #[Groups(['supplierOrder:read'])]
    private Supplier $supplier;

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

    public function getSupplierOrderDate(): ?DateTimeInterface
    {
        return $this->supplierOrderDate;
    }

    public function setSupplierOrderDate(?DateTimeInterface $supplierOrderDate): self
    {
        $this->supplierOrderDate = $supplierOrderDate;

        return $this;
    }

    public function getSupplierOrderCreationDate(): ?DateTimeInterface
    {
        return $this->supplierOrderCreationDate;
    }

    public function setSupplierOrderCreationDate(?DateTimeInterface $supplierOrderCreationDate): self
    {
        $this->supplierOrderCreationDate = $supplierOrderCreationDate;

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

    public function getSupplierOrderPos(): Collection
    {
        return $this->supplierOrderPos;
    }

    public function getSupplier(): Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(Supplier $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }
}
