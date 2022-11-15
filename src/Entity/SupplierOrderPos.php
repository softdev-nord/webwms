<?php

namespace WebWMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'supplier_order_pos')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\SupplierOrderPosRepository')]
class SupplierOrderPos
{
    /** Many Supplier Order Positions have one Supplier Order. This is the owning side. */
    #[ORM\ManyToOne(targetEntity: SupplierOrder::class, inversedBy: 'details')]
    #[ORM\JoinColumn(name: 'supplier_order_id', referencedColumnName: 'id')]
    protected Collection $supplierOrders;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'supplier_order_id', type: 'integer', nullable: false)]
    private ?int $supplierOrderId;

    #[ORM\Column(name: 'article_id', type: 'integer', nullable: false)]
    private ?int $articleId;

    #[ORM\Column(name: 'supplier_order_pos_quantity', type: 'integer', nullable: false)]
    private ?int $supplierOrderPosQuantity;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->supplierOrders = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id): self
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

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function setArticleId(int $articleId): self
    {
        $this->articleId = $articleId;

        return $this;
    }

    public function getSupplierOrderPosQuantity(): int
    {
        return $this->supplierOrderPosQuantity;
    }

    public function setSupplierOrderPosQuantity(int $supplierOrderPosQuantity): self
    {
        $this->supplierOrderPosQuantity = $supplierOrderPosQuantity;

        return $this;
    }

    public function getSupplierOrders(): Collection
    {
        return $this->supplierOrders;
    }

    public function setSupplierOrders(Collection $supplierOrders): self
    {
        $this->supplierOrders = $supplierOrders;

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
}
