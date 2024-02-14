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
use WebWMS\Repository\CustomerOrderPosRepository;

/**
 * @package:    WebWMS\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderPosEntity
 */
#[ORM\Table(name: 'customer_orders_pos')]
#[ORM\Entity(repositoryClass: CustomerOrderPosRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'groups' => ['customerOrderPos:read'],
            ]
        ),
        new GetCollection(
            normalizationContext: [
                'groups' => ['customerOrderPos:read'],
            ]
        ),
        new Post(
            denormalizationContext: [
                'groups' => ['customerOrderPos:write'],
            ]
        ),
        new Put(
            denormalizationContext: [
                'groups' => ['customerOrderPos:write'],
            ]
        ),
        new Patch(
            denormalizationContext: [
                'groups' => ['customerOrderPos:write'],
            ]
        ),
        new Delete(
            denormalizationContext: [
                'groups' => ['customerOrderPos:write'],
            ]
        ),
    ],
    formats: ['json'],
    normalizationContext: ['groups' => ['customerOrderPos:read']],
    denormalizationContext: ['groups' => ['customerOrderPos:write']]
)]
class CustomerOrderPosEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(name: 'customer_order_id', type: Types::INTEGER, nullable: false)]
    #[Groups(['customerOrderPos:read', 'customerOrderPos:write'])]
    private int $customerOrderId;

    #[ORM\Column(name: 'article_id', type: Types::INTEGER, nullable: false)]
    #[Groups(['customerOrderPos:read', 'customerOrderPos:write'])]
    private int $articleId;

    #[ORM\Column(name: 'article_nr', type: Types::STRING, length: 50, nullable: false)]
    #[Groups(['customerOrderPos:read', 'customerOrderPos:write'])]
    private string $articleNr;

    #[ORM\Column(name: 'article_name', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['customerOrderPos:read', 'customerOrderPos:write'])]
    private string $articleName;

    #[ORM\Column(name: 'quantity', type: Types::INTEGER, nullable: false)]
    #[Groups(['customerOrderPos:read', 'customerOrderPos:write'])]
    private int $quantity;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['customerOrderPos:read', 'customerOrderPos:write'])]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['customerOrderPos:read', 'customerOrderPos:write'])]
    private ?DateTimeInterface $updatedAt = null;

    /** Many CustomerEntity Order Positions have one CustomerEntity Order. This is the owning side. */
    #[ORM\ManyToOne(targetEntity: CustomerOrderEntity::class, inversedBy: 'customerOrderPos')]
    private CustomerOrderEntity $customerOrder;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCustomerOrderId(): int
    {
        return $this->customerOrderId;
    }

    public function setCustomerOrderId(int $customerOrderId): self
    {
        $this->customerOrderId = $customerOrderId;

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

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCustomerOrder(): CustomerOrderEntity
    {
        return $this->customerOrder;
    }

    public function setCustomerOrder(CustomerOrderEntity $customerOrderEntity): self
    {
        $this->customerOrder = $customerOrderEntity;

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
}
