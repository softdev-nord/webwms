<?php

namespace WebWMS\Entity;

use Symfony\Component\Validator\Constraints\Type;
use WebWMS\Repository\CustomerOrderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CustomerOrderRepository::class)
 * @ORM\Table(name="`customer_orders`")
 */
class CustomerOrder
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
    private $customer_order_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $usr_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $customer_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $customer_order_nr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $customer_order_reference;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $customer_order_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $customer_order_order_date;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getUsrId(): ?int
    {
        return $this->usr_id;
    }

    /**
     * @param int $usr_id
     * @return $this
     */
    public function setUsrId(int $usr_id): self
    {
        $this->usr_id = $usr_id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->customer_id;
    }

    /**
     * @param int $customer_id
     * @return $this
     */
    public function setCustomerId(int $customer_id): self
    {
        $this->customer_id = $customer_id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCustomerOrderId(): ?int
    {
        return $this->customer_order_id;
    }

    /**
     * @param int $customer_order_id
     * @return $this
     */
    public function setCustomerOrderId(int $customer_order_id): self
    {
        $this->customer_order_id = $customer_order_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomerOrderNr(): ?string
    {
        return $this->customer_order_nr;
    }

    /**
     * @param string $customer_order_nr
     * @return $this
     */
    public function setCustomerOrderNr(string $customer_order_nr): self
    {
        $this->customer_order_nr = $customer_order_nr;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomerOrderReference(): ?string
    {
        return $this->customer_order_reference;
    }

    /**
     * @param string|null $customer_order_reference
     * @return $this
     */
    public function setCustomerOrderReference(?string $customer_order_reference): self
    {
        $this->customer_order_reference = $customer_order_reference;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCustomerOrderDate(): ?\DateTimeInterface
    {
        return $this->customer_order_date;
    }

    /**
     * @param \DateTimeInterface|null $customer_order_date
     * @return $this
     */
    public function setCustomerOrderDate(?\DateTimeInterface $customer_order_date): self
    {
        $this->customer_order_date = $customer_order_date;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCustomerOrderOrderDate(): ?\DateTimeInterface
    {
        return $this->customer_order_order_date;
    }

    /**
     * @param \DateTimeInterface|null $customer_order_order_date
     * @return CustomerOrder
     */
    public function setCustomerOrderOrderDate(?\DateTimeInterface $customer_order_order_date): self
    {
        $this->customer_order_order_date = $customer_order_order_date;

        return $this;
    }
}
