<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Components\Entity\ModelEntity;

#[ORM\Table(name: 'customer_orders')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\CustomerOrderRepository')]
class CustomerOrder extends ModelEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'customer_order_id', type: 'integer', nullable: false)]
    private int $customerOrderId;

    #[ORM\Column(name: 'usr_id', type: 'integer', nullable: false)]
    private int $usrId;

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
    #[ORM\OneToMany(mappedBy: 'customerOrders', targetEntity: CustomerOrderPos::class)]
    protected Collection $details;

    public function __construct()
    {
        $this->details = new ArrayCollection();
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

    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function setDetails(array $details): CustomerOrder
    {
        return $this->setOneToMany($details, CustomerOrderPos::class, 'details', 'customerOrder');
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
