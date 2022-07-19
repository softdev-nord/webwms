<?php

declare(strict_types=1);

namespace WebWMS\Entity;

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
    private ?int $supplier_order_id;

    /**
     * @ORM\Column(type="integer")
     */
    private mixed $usr_id;

    /**
     * @ORM\Column(type="integer")
     */
    private mixed $supplier_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private mixed $supplier_order_nr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private mixed $supplier_order_reference;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $supplier_order_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $supplier_order_order_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $supplier_order_created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $supplier_order_updated_at;

    /**
     * INVERSE SIDE.
     *
     * @var SupplierOrderPos
     *
     * @ORM\OneToMany(targetEntity="\WebWMS\Entity\SupplierOrderPos", mappedBy="supplier_orders", orphanRemoval=true, cascade={"persist"})
     */
    protected SupplierOrderPos $details;

    /**
     * @var Supplier
     *
     * @ORM\ManyToOne(targetEntity="\WebWMS\Entity\Supplier", inversedBy="supplier_orders")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    protected Supplier $supplier;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(mixed $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getSupplierOrderId(): ?int
    {
        return $this->supplier_order_id;
    }

    /**
     * @param mixed $supplier_order_id
     */
    public function setSupplierOrderId(mixed $supplier_order_id): void
    {
        $this->supplier_order_id = $supplier_order_id;
    }

    /**
     * @return mixed
     */
    public function getUsrId(): mixed
    {
        return $this->usr_id;
    }

    /**
     * @param mixed $usr_id
     */
    public function setUsrId(mixed $usr_id): void
    {
        $this->usr_id = $usr_id;
    }

    /**
     * @return mixed
     */
    public function getSupplierId(): mixed
    {
        return $this->supplier_id;
    }

    /**
     * @param mixed $supplier_id
     */
    public function setSupplierId(mixed $supplier_id): void
    {
        $this->supplier_id = $supplier_id;
    }

    /**
     * @return mixed
     */
    public function getSupplierOrderNr(): mixed
    {
        return $this->supplier_order_nr;
    }

    /**
     * @param mixed $supplier_order_nr
     */
    public function setSupplierOrderNr(mixed $supplier_order_nr): void
    {
        $this->supplier_order_nr = $supplier_order_nr;
    }

    /**
     * @return mixed
     */
    public function getSupplierOrderReference(): mixed
    {
        return $this->supplier_order_reference;
    }

    /**
     * @param mixed $supplier_order_reference
     */
    public function setSupplierOrderReference(mixed $supplier_order_reference): void
    {
        $this->supplier_order_reference = $supplier_order_reference;
    }

    /**
     * @return mixed
     */
    public function getSupplierOrderDate(): mixed
    {
        return $this->supplier_order_date;
    }

    /**
     * @param mixed $supplier_order_date
     */
    public function setSupplierOrderDate(mixed $supplier_order_date): void
    {
        $this->supplier_order_date = $supplier_order_date;
    }

    /**
     * @return mixed
     */
    public function getSupplierOrderOrderDate(): mixed
    {
        return $this->supplier_order_order_date;
    }

    /**
     * @param mixed $supplier_order_order_date
     */
    public function setSupplierOrderOrderDate(mixed $supplier_order_order_date): void
    {
        $this->supplier_order_order_date = $supplier_order_order_date;
    }

    /**
     * @return mixed
     */
    public function getSupplierOrderCreatedAt(): mixed
    {
        return $this->supplier_order_created_at;
    }

    /**
     * @param mixed $supplier_order_created_at
     */
    public function setSupplierOrderCreatedAt(mixed $supplier_order_created_at): void
    {
        $this->supplier_order_created_at = $supplier_order_created_at;
    }

    /**
     * @return mixed
     */
    public function getSupplierOrderUpdatedAt(): mixed
    {
        return $this->supplier_order_updated_at;
    }

    /**
     * @param mixed $supplier_order_updated_at
     */
    public function setSupplierOrderUpdatedAt(mixed $supplier_order_updated_at): void
    {
        $this->supplier_order_updated_at = $supplier_order_updated_at;
    }

    public function getDetails(): SupplierOrderPos
    {
        return $this->details;
    }

    /**
     * @param SupplierOrderPos[]|null $details
     *
     * @return ModelEntity|SupplierOrder|SupplierOrderPos
     */
    public function setDetails(?array $details)
    {
        return $this->setOneToMany($details, SupplierOrderPos::class, 'details', 'customer_order');
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
