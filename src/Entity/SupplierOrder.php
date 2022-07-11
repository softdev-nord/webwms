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
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $supplier_order_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $usr_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $supplier_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $supplier_order_nr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $supplier_order_reference;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $supplier_order_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $supplier_order_order_date;

    /**
     * INVERSE SIDE.
     *
     * @var SupplierOrderPos
     *
     * @ORM\OneToMany(targetEntity="\WebWMS\Entity\SupplierOrderPos", mappedBy="supplier_orders", orphanRemoval=true, cascade={"persist"})
     */
    protected $details;

    /**
     * @var Supplier
     *
     * @ORM\ManyToOne(targetEntity="\WebWMS\Entity\Supplier", inversedBy="supplier_orders")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    protected $supplier;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getSupplierOrderId()
    {
        return $this->supplier_order_id;
    }

    /**
     * @param mixed $supplier_order_id
     */
    public function setSupplierOrderId($supplier_order_id): void
    {
        $this->supplier_order_id = $supplier_order_id;
    }

    /**
     * @return mixed
     */
    public function getUsrId()
    {
        return $this->usr_id;
    }

    /**
     * @param mixed $usr_id
     */
    public function setUsrId($usr_id): void
    {
        $this->usr_id = $usr_id;
    }

    /**
     * @return mixed
     */
    public function getSupplierId()
    {
        return $this->supplier_id;
    }

    /**
     * @param mixed $supplier_id
     */
    public function setSupplierId($supplier_id): void
    {
        $this->supplier_id = $supplier_id;
    }

    /**
     * @return mixed
     */
    public function getSupplierOrderNr()
    {
        return $this->supplier_order_nr;
    }

    /**
     * @param mixed $supplier_order_nr
     */
    public function setSupplierOrderNr($supplier_order_nr): void
    {
        $this->supplier_order_nr = $supplier_order_nr;
    }

    /**
     * @return mixed
     */
    public function getSupplierOrderReference()
    {
        return $this->supplier_order_reference;
    }

    /**
     * @param mixed $supplier_order_reference
     */
    public function setSupplierOrderReference($supplier_order_reference): void
    {
        $this->supplier_order_reference = $supplier_order_reference;
    }

    /**
     * @return mixed
     */
    public function getSupplierOrderDate()
    {
        return $this->supplier_order_date;
    }

    /**
     * @param mixed $supplier_order_date
     */
    public function setSupplierOrderDate($supplier_order_date): void
    {
        $this->supplier_order_date = $supplier_order_date;
    }

    /**
     * @return mixed
     */
    public function getSupplierOrderOrderDate()
    {
        return $this->supplier_order_order_date;
    }

    /**
     * @param mixed $supplier_order_order_date
     */
    public function setSupplierOrderOrderDate($supplier_order_order_date): void
    {
        $this->supplier_order_order_date = $supplier_order_order_date;
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
