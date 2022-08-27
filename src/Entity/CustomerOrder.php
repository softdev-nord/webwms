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
    private ?int $customer_order_id;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $usr_id;

    /**
     * @ORM\Column(name="customer_id", type="integer")
     */
    private ?int $customer_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $customer_order_nr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $customer_order_reference;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $customer_order_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $customer_order_order_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $customer_order_created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $customer_order_updated_at;

    /**
     * INVERSE SIDE.
     *
     * @var \Doctrine\Common\Collections\ArrayCollection<\WebWMS\Entity\CustomerOrderPos>
     *
     * @ORM\OneToMany(targetEntity="\WebWMS\Entity\CustomerOrderPos", mappedBy="customer_orders")
     */
    protected $details;

    /**
     * @var \WebWMS\Entity\Customer
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
        return $this->usr_id;
    }

    /**
     * @return $this
     */
    public function setUsrId(int $usr_id): self
    {
        $this->usr_id = $usr_id;

        return $this;
    }

    public function getCustomerId(): ?int
    {
        return $this->customer_id;
    }

    /**
     * @return $this
     */
    public function setCustomerId(int $customer_id): self
    {
        $this->customer_id = $customer_id;

        return $this;
    }

    public function getCustomerOrderId(): ?int
    {
        return $this->customer_order_id;
    }

    /**
     * @return $this
     */
    public function setCustomerOrderId(int $customer_order_id): self
    {
        $this->customer_order_id = $customer_order_id;

        return $this;
    }

    public function getCustomerOrderNr(): ?string
    {
        return $this->customer_order_nr;
    }

    /**
     * @return $this
     */
    public function setCustomerOrderNr(string $customer_order_nr): self
    {
        $this->customer_order_nr = $customer_order_nr;

        return $this;
    }

    public function getCustomerOrderReference(): ?string
    {
        return $this->customer_order_reference;
    }

    /**
     * @return $this
     */
    public function setCustomerOrderReference(?string $customer_order_reference): self
    {
        $this->customer_order_reference = $customer_order_reference;

        return $this;
    }

    public function getCustomerOrderDate(): ?\DateTimeInterface
    {
        return $this->customer_order_date;
    }

    /**
     * @return $this
     */
    public function setCustomerOrderDate(?\DateTimeInterface $customer_order_date): self
    {
        $this->customer_order_date = $customer_order_date;

        return $this;
    }

    public function getCustomerOrderOrderDate(): ?\DateTimeInterface
    {
        return $this->customer_order_order_date;
    }

    /**
     * @return CustomerOrder
     */
    public function setCustomerOrderOrderDate(?\DateTimeInterface $customer_order_order_date): self
    {
        $this->customer_order_order_date = $customer_order_order_date;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\WebWMS\Entity\CustomerOrderPos>
     */
    public function getDetails(): ArrayCollection
    {
        return $this->details;
    }

    /**
     * @param \WebWMS\Entity\CustomerOrderPos[]|null $details
     */
    public function setDetails(?array $details): CustomerOrder
    {
        return $this->setOneToMany($details, \WebWMS\Entity\CustomerOrderPos::class, 'details', 'customer_order');
    }

    /**
     * @return \WebWMS\Entity\Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @param \WebWMS\Entity\Customer $customer
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return mixed
     */
    public function getCustomerOrderCreatedAt(): mixed
    {
        return $this->customer_order_created_at;
    }

    /**
     * @param mixed $customer_order_created_at
     */
    public function setCustomerOrderCreatedAt(mixed $customer_order_created_at): void
    {
        $this->customer_order_created_at = $customer_order_created_at;
    }

    /**
     * @return mixed
     */
    public function getCustomerOrderUpdatedAt(): mixed
    {
        return $this->customer_order_updated_at;
    }

    /**
     * @param mixed $customer_order_updated_at
     */
    public function setCustomerOrderUpdatedAt(mixed $customer_order_updated_at): void
    {
        $this->customer_order_updated_at = $customer_order_updated_at;
    }
}
