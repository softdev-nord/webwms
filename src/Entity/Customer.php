<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\CustomerRepository;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 */
class Customer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $customer_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $customer_nr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $customer_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $customer_address_addition;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $customer_address_street;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $customer_address_street_nr;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $customer_country_code;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $customer_zip_code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $customer_city;

    /**
     * INVERSE SIDE
     * The customer_orders property is the inverse side of the association between customer and customer orders.
     * The association is joined over the customer id field and the userID field of the customer order.
     *
     * @var ArrayCollection<\WebWMS\Entity\Customer>
     *
     * @ORM\OneToMany(targetEntity="WebWMS\Entity\CustomerOrder", mappedBy="customer")
     */
    protected $customer_orders;

    public function __construct()
    {
        $this->customer_orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerId(): ?int
    {
        return $this->customer_id;
    }

    public function setCustomerId(int $customer_id): self
    {
        $this->customer_id = $customer_id;

        return $this;
    }

    public function getCustomerNr(): ?int
    {
        return $this->customer_nr;
    }

    public function setCustomerNr(int $customer_nr): self
    {
        $this->customer_nr = $customer_nr;

        return $this;
    }

    public function getCustomerName(): ?string
    {
        return $this->customer_name;
    }

    public function setCustomerName(string $customer_name): self
    {
        $this->customer_name = $customer_name;

        return $this;
    }

    public function getCustomerAddressAddition(): ?string
    {
        return $this->customer_address_addition;
    }

    public function setCustomerAddressAddition(?string $customer_address_addition): self
    {
        $this->customer_address_addition = $customer_address_addition;

        return $this;
    }

    public function getCustomerAddressStreet(): ?string
    {
        return $this->customer_address_street;
    }

    public function setCustomerAddressStreet(string $customer_address_street): self
    {
        $this->customer_address_street = $customer_address_street;

        return $this;
    }

    public function getCustomerAddressStreetNr(): ?string
    {
        return $this->customer_address_street_nr;
    }

    public function setCustomerAddressStreetNr(string $customer_address_street_nr): self
    {
        $this->customer_address_street_nr = $customer_address_street_nr;

        return $this;
    }

    public function getCustomerCountryCode(): ?string
    {
        return $this->customer_country_code;
    }

    public function setCustomerCountryCode(string $customer_country_code): self
    {
        $this->customer_country_code = $customer_country_code;

        return $this;
    }

    public function getCustomerZipCode(): ?string
    {
        return $this->customer_zip_code;
    }

    public function setCustomerZipCode(string $customer_zip_code): self
    {
        $this->customer_zip_code = $customer_zip_code;

        return $this;
    }

    public function getCustomerCity(): ?string
    {
        return $this->customer_city;
    }

    public function setCustomerCity(string $customer_city): self
    {
        $this->customer_city = $customer_city;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customer_id,
            'customer_nr' => $this->customer_nr,
            'customer_name' => $this->customer_name,
            'customer_address_addition' => $this->customer_address_addition,
            'customer_address_street' => $this->customer_address_street,
            'customer_address_street_nr' => $this->customer_address_street_nr,
            'customer_country_code' => $this->customer_country_code,
            'customer_zip_code' => $this->customer_zip_code,
            'customer_city' => $this->customer_city,
        ];
    }

    /**
     * Returns an array collection of WebWMS\Entity\CustomerOrder model instances, which
     * contains all data about the a single customer order. The association is defined over
     * the Customer.customer_orders property (INVERSE SIDE) and the CustomerOrder.customer (OWNING SIDE) property.
     * The order data is joined over the customer_orders.customer_id field.
     *
     * @return ArrayCollection<\WebWMS\Entity\CustomerOrder>
     */
    public function getCustomerOrders(): ArrayCollection
    {
        return $this->customer_orders;
    }

    /**
     * Setter function for the customer_orders association property which contains many instances of the WebWMS\Entity\CustomerOrder model which
     * contains all data about the a single customer order. The association is defined over
     * the Customer.customer_orders property (INVERSE SIDE) and the CustomerOrder.customer (OWNING SIDE) property.
     * The order data is joined over the customer_orders.customer_id field.
     *
     * @param ArrayCollection<\WebWMS\Entity\CustomerOrder>|null $customer_orders
     */
    public function setCustomerOrders(ArrayCollection $customer_orders): Customer
    {
        $this->customer_orders = $customer_orders;

        return $this;
    }
}
