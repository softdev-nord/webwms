<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Components\Entity\ModelEntity;
use WebWMS\Repository\CustomerOrderRepository;

/**
 * @ORM\Entity(repositoryClass=CustomerOrderRepository::class)
 * @ORM\Table(name="`customer_orders`")
 */
class CustomerOrder extends ModelEntity
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
    private ?int $customerOrderId;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $usrId;

    /**
     * @ORM\Column(name="customer_id", type="integer")
     */
    private ?int $customerId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $customerOrderNr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $customerOrderReference;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $customerOrderDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $customerOrderOrderDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $customerOrderCreatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $customerOrderUpdatedAt;

    /**
     * INVERSE SIDE.
     *
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\WebWMS\Entity\CustomerOrderPos", mappedBy="customer_orders")
     */
    protected ArrayCollection $details;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="\WebWMS\Entity\Customer", inversedBy="customer_orders")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    protected Customer $customer;

    public function __construct()
    {
        $this->details = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsrId(): ?int
    {
        return $this->usrId;
    }

    public function setUsrId(int $usrId): self
    {
        $this->usrId = $usrId;

        return $this;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getCustomerOrderId(): ?int
    {
        return $this->customerOrderId;
    }

    public function setCustomerOrderId(int $customerOrderId): self
    {
        $this->customerOrderId = $customerOrderId;

        return $this;
    }

    public function getCustomerOrderNr(): ?string
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

    public function getCustomerOrderOrderDate(): ?\DateTimeInterface
    {
        return $this->customerOrderOrderDate;
    }

    public function setCustomerOrderOrderDate(?\DateTimeInterface $customerOrderOrderDate): self
    {
        $this->customerOrderOrderDate = $customerOrderOrderDate;

        return $this;
    }

    public function getDetails(): ArrayCollection
    {
        return $this->details;
    }

    public function setDetails(?array $details): ?CustomerOrder
    {
        return $this->setOneToMany($details, CustomerOrderPos::class, 'details', 'customer_order');
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function getCustomerOrderCreatedAt(): mixed
    {
        return $this->customerOrderCreatedAt;
    }

    public function setCustomerOrderCreatedAt(mixed $customerOrderCreatedAt): void
    {
        $this->customerOrderCreatedAt = $customerOrderCreatedAt;
    }

    public function getCustomerOrderUpdatedAt(): mixed
    {
        return $this->customerOrderUpdatedAt;
    }

    public function setCustomerOrderUpdatedAt(mixed $customerOrderUpdatedAt): void
    {
        $this->customerOrderUpdatedAt = $customerOrderUpdatedAt;
    }
}
