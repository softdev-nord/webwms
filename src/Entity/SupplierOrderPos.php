<?php

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\SupplierOrderPosRepository;

/**
 * @ORM\Entity(repositoryClass=SupplierOrderPosRepository::class)
 */
class SupplierOrderPos
{
    /**
     * @ORM\ManyToOne(targetEntity="SupplierOrder", inversedBy="details")
     * @ORM\JoinColumn(name="supplier_order_id", referencedColumnName="id")
     */
    protected SupplierOrder $supplierOrders;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $supplierOrderId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $articleId;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $supplierOrderPosQuantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSupplierOrderId(): ?int
    {
        return $this->supplierOrderId;
    }

    public function setSupplierOrderId(?int $supplierOrderId): void
    {
        $this->supplierOrderId = $supplierOrderId;
    }

    public function getArticleId(): ?int
    {
        return $this->articleId;
    }

    public function setArticleId(?int $articleId): void
    {
        $this->articleId = $articleId;
    }

    public function getSupplierOrderPosQuantity(): ?int
    {
        return $this->supplierOrderPosQuantity;
    }

    public function setSupplierOrderPosQuantity(?int $supplierOrderPosQuantity): void
    {
        $this->supplierOrderPosQuantity = $supplierOrderPosQuantity;
    }

    public function getSupplierOrders(): SupplierOrder
    {
        return $this->supplierOrders;
    }

    public function setSupplierOrders(SupplierOrder $supplierOrders): void
    {
        $this->supplierOrders = $supplierOrders;
    }
}
