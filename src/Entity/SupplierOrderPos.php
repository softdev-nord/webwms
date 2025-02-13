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
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Repository\SupplierOrderPosRepository;

#[ClassInformation(
    package: 'WebWMS\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'SupplierOrderPos'
)]
#[ORM\Table(name: 'supplier_order_pos')]
#[ORM\Entity(repositoryClass: SupplierOrderPosRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'groups' => ['supplierOrderPos:read'],
            ]
        ),
        new GetCollection(
            normalizationContext: [
                'groups' => ['supplierOrderPos:read'],
            ]
        ),
        new Post(
            denormalizationContext: [
                'groups' => ['supplierOrderPos:write'],
            ]
        ),
        new Put(
            denormalizationContext: [
                'groups' => ['supplierOrderPos:write'],
            ]
        ),
        new Patch(
            denormalizationContext: [
                'groups' => ['supplierOrderPos:write'],
            ]
        ),
        new Delete(
            denormalizationContext: [
                'groups' => ['supplierOrderPos:write'],
            ]
        ),
    ],
    formats: ['json'],
    normalizationContext: ['groups' => ['supplierOrderPos:read']],
    denormalizationContext: ['groups' => ['supplierOrderPos:write']]
)]
class SupplierOrderPos
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(name: 'supplier_order_id', type: Types::INTEGER, nullable: false)]
    #[Groups(['supplierOrderPos:read', 'supplierOrderPos:write'])]
    private int $supplierOrderId;

    #[ORM\Column(name: 'article_id', type: Types::INTEGER, nullable: false)]
    #[Groups(['supplierOrderPos:read', 'supplierOrderPos:write'])]
    private int $articleId;

    #[ORM\Column(name: 'article_nr', type: Types::STRING, length: 50, nullable: false)]
    #[Groups(['supplierOrderPos:read', 'supplierOrderPos:write'])]
    private string $articleNr;

    #[ORM\Column(name: 'article_name', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['supplierOrderPos:read', 'supplierOrderPos:write'])]
    private string $articleName;

    #[ORM\Column(name: 'supplier_order_pos_quantity', type: Types::INTEGER, nullable: false)]
    #[Groups(['supplierOrderPos:read', 'supplierOrderPos:write'])]
    private int $supplierOrderPosQuantity;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['supplierOrderPos:read', 'supplierOrderPos:write'])]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['supplierOrderPos:read', 'supplierOrderPos:write'])]
    private ?DateTimeInterface $updatedAt = null;

    /** Many Supplier Order Positions have one Supplier Order. This is the owning side. */
    #[ORM\ManyToOne(targetEntity: SupplierOrder::class, inversedBy: 'supplierOrderPos')]
    private SupplierOrder $supplierOrder;

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

    public function getArticleId(): int
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

    public function getSupplierOrderPosQuantity(): int
    {
        return $this->supplierOrderPosQuantity;
    }

    public function setSupplierOrderPosQuantity(int $supplierOrderPosQuantity): self
    {
        $this->supplierOrderPosQuantity = $supplierOrderPosQuantity;

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

    public function getSupplierOrder(): SupplierOrder
    {
        return $this->supplierOrder;
    }

    public function setSupplierOrder(SupplierOrder $supplierOrder): self
    {
        $this->supplierOrder = $supplierOrder;

        return $this;
    }
}
