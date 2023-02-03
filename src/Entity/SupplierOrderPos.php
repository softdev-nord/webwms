<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'supplier_order_pos')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\SupplierOrderPosRepository')]
class SupplierOrderPos
{
    /** Many Supplier Order Positions have one Supplier Order. This is the owning side. */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'supplier_order_id', type: 'integer', nullable: false)]
    private ?int $supplierOrderId;

    #[ORM\Column(name: 'article_id', type: 'integer', nullable: false)]
    private ?int $articleId;

    #[ORM\Column(name: 'article_nr', type: 'string', length: 50, nullable: false)]
    private string $articleNr;

    #[ORM\Column(name: 'article_name', type: 'string', length: 255, nullable: false)]
    private string $articleName;

    #[ORM\Column(name: 'supplier_order_pos_quantity', type: 'integer', nullable: false)]
    private ?int $supplierOrderPosQuantity;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'supplierOrderPos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SupplierOrder $supplierOrder = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

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

    public function getArticleId(): ?int
    {
        return $this->articleId;
    }

    public function setArticleId(int $articleId): self
    {
        $this->articleId = $articleId;

        return $this;
    }

    public function getArticleNr(): string
    {
        return $this->articleNr;
    }

    public function setArticleNr(string $articleNr): self
    {
        $this->articleNr = $articleNr;

        return $this;
    }

    public function getArticleName(): string
    {
        return $this->articleName;
    }

    public function setArticleName(string $articleName): self
    {
        $this->articleName = $articleName;

        return $this;
    }

    public function getSupplierOrderPosQuantity(): ?int
    {
        return $this->supplierOrderPosQuantity;
    }

    public function setSupplierOrderPosQuantity(int $supplierOrderPosQuantity): self
    {
        $this->supplierOrderPosQuantity = $supplierOrderPosQuantity;

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

    public function getSupplierOrder(): ?SupplierOrder
    {
        return $this->supplierOrder;
    }

    public function setSupplierOrder(?SupplierOrder $supplierOrder): self
    {
        $this->supplierOrder = $supplierOrder;

        return $this;
    }
}
