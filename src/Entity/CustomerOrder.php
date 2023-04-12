<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @package:    WebWMS\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrder
 */
#[ORM\Table(name: 'customer_orders')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\CustomerOrderRepository')]
#[ApiResource(
    extraProperties: [
        'standard_put' => true,
    ],
)]
class CustomerOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'customer_order_id', type: 'integer', nullable: false)]
    private int $customerOrderId;

    #[ORM\Column(name: 'usr_id', type: 'integer', nullable: false)]
    private int $usrId;

    #[ORM\OneToMany(mappedBy: 'role', targetEntity: CustomerOrderPos::class)]
    #[ORM\Column(name: 'customer_id', type: 'integer', nullable: false)]
    private int $customerId;

    #[ORM\Column(name: 'customer_order_nr', type: 'string', length: 255, nullable: false)]
    private string $customerOrderNr;

    #[ORM\Column(name: 'customer_order_reference', type: 'string', length: 255, nullable: true)]
    private ?string $customerOrderReference;

    #[ORM\Column(name: 'customer_order_date', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $customerOrderDate;

    #[ORM\Column(name: 'customer_order_creation_date', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $customerOrderCreationDate;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt;

    /** One Customer Order has many Customer Order Positions. This is the inverse side. */
    #[ORM\OneToMany(
        mappedBy: 'customerOrder',
        targetEntity: CustomerOrderPos::class,
        cascade: ['persist'],
        fetch: 'EAGER'
    )]
    private Collection|ArrayCollection $customerOrderPos;

    #[ORM\OneToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'customer_id')]
    private ?Customer $customer;

    public function __construct()
    {
        $this->customerOrderPos = new ArrayCollection();
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer|null $customer): void
    {
        $this->customer = $customer;
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

    public function addCustomerOrderPos(CustomerOrderPos $customerOrderPos): self
    {
        if (!$this->customerOrderPos->contains($customerOrderPos)) {
            $this->customerOrderPos->add($customerOrderPos);
            $customerOrderPos->setCustomerOrder($this);
        }

        return $this;
    }

    public function removeCustomerOrderPos(CustomerOrderPos $customerOrderPos): self
    {
        if ($this->customerOrderPos->removeElement($customerOrderPos)) {
            // set the owning side to null (unless already changed)
            if ($customerOrderPos->getCustomerOrder() === $this) {
                $customerOrderPos->setCustomerOrder(null);
            }
        }

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
