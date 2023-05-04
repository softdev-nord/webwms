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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @package:    WebWMS\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrder
 */
#[ORM\Table(name: 'customer_orders')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\CustomerOrderRepository')]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'skip_null_values' => false,
                'groups' => ['customerOrder:read', 'customerOrderPos:read', 'customer:read']]
        ),
        new GetCollection(
            normalizationContext: [
                'skip_null_values' => false,
                'groups' => ['customerOrder:read', 'customerOrderPos:read', 'customer:read']]
        ),
        new Post(
            denormalizationContext: [
                'groups' => ['customerOrder:write', 'customerOrderPos:write', 'customer:write']]
        ),
        new Put(
            denormalizationContext: [
                'groups' => ['customerOrder:write', 'customerOrderPos:write', 'customer:write']]
        ),
        new Patch(
            denormalizationContext: [
                'groups' => ['customerOrder:write', 'customerOrderPos:write', 'customer:write']]
        ),
        new Delete(
            denormalizationContext: [
                'groups' => ['customerOrder:write', 'customerOrderPos:write', 'customer:write']]
        ),
    ],
    formats: ['json'],
    normalizationContext: ['groups' => ['customerOrder:read']],
    denormalizationContext: ['groups' => ['customerOrder:write']]
)]
class CustomerOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'customer_order_id', type: 'integer', nullable: false)]
    #[Groups(['customerOrder:read', 'customerOrder:write'])]
    private int $customerOrderId;

    #[ORM\Column(name: 'usr_id', type: 'integer', nullable: false)]
    #[Groups(['customerOrder:read', 'customerOrder:write'])]
    private int $usrId;

    #[ORM\Column(name: 'customer_id', type: 'integer', nullable: false)]
    #[Groups(['customerOrder:read', 'customerOrder:write'])]
    private int $customerId;

    #[ORM\Column(name: 'customer_order_nr', type: 'string', length: 255, nullable: false)]
    #[Groups(['customerOrder:read', 'customerOrder:write'])]
    private string $customerOrderNr;

    #[ORM\Column(name: 'customer_order_reference', type: 'string', length: 255, nullable: true)]
    #[Groups(['customerOrder:read', 'customerOrder:write'])]
    private ?string $customerOrderReference;

    #[ORM\Column(name: 'customer_order_date', type: 'datetime', nullable: true)]
    #[Groups(['customerOrder:read', 'customerOrder:write'])]
    private ?\DateTimeInterface $customerOrderDate;

    #[ORM\Column(name: 'customer_order_creation_date', type: 'datetime', nullable: true)]
    #[Groups(['customerOrder:read', 'customerOrder:write'])]
    private ?\DateTimeInterface $customerOrderCreationDate;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    #[Groups(['customerOrder:read', 'customerOrder:write'])]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    #[Groups(['customerOrder:read', 'customerOrder:write'])]
    private ?\DateTimeInterface $updatedAt;

    /** One Customer Order has many Customer Order Positions. This is the inverse side. */
    #[ORM\OneToMany(
        mappedBy: 'customerOrder',
        targetEntity: CustomerOrderPos::class,
        cascade: ['persist'],
        fetch: 'EAGER'
    )]
    #[Groups(['customerOrder:read'])]
    private Collection|ArrayCollection $customerOrderPos;

    /** Many Customer Orders has one Customer. This is the owning side. */
    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'customerOrder')]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id')]
    #[Groups(['customerOrder:read'])]
    private Customer $customer;

    public function __construct()
    {
        $this->customerOrderPos = new ArrayCollection();
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
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

    public function getUsrId(): int
    {
        return $this->usrId;
    }

    public function setUsrId(int $usrId): self
    {
        $this->usrId = $usrId;

        return $this;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;

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

    public function getCustomerOrderNr(): string
    {
        return $this->customerOrderNr;
    }

    public function setCustomerOrderNr(string $customerOrderNr): self
    {
        $this->customerOrderNr = $customerOrderNr;

        return $this;
    }

    public function getCustomerOrderReference(): ?string
    {
        return $this->customerOrderReference;
    }

    public function setCustomerOrderReference(?string $customerOrderReference): self
    {
        $this->customerOrderReference = $customerOrderReference;

        return $this;
    }

    public function getCustomerOrderDate(): ?\DateTimeInterface
    {
        return $this->customerOrderDate;
    }

    public function setCustomerOrderDate(?\DateTimeInterface $customerOrderDate): self
    {
        $this->customerOrderDate = $customerOrderDate;

        return $this;
    }

    public function getCustomerOrderCreationDate(): ?\DateTimeInterface
    {
        return $this->customerOrderCreationDate;
    }

    public function setCustomerOrderCreationDate(?\DateTimeInterface $customerOrderCreationDate): self
    {
        $this->customerOrderCreationDate = $customerOrderCreationDate;

        return $this;
    }

    public function getCustomerOrderPos(): Collection
    {
        return $this->customerOrderPos;
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
