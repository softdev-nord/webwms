<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\SupplierRepository;

/**
 * @ORM\Entity(repositoryClass=SupplierRepository::class)
 */
class Supplier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="integer")
     */
    private mixed $supplier_id;

    /**
     * @ORM\Column(type="integer")
     */
    private mixed $supplier_nr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private mixed $supplier_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private mixed $supplier_address_addition;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private mixed $supplier_address_street;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private mixed $supplier_address_street_nr;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private mixed $supplier_address_country_code;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private mixed $supplier_address_zipcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private mixed $supplier_address_city;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $supplier_created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $supplier_updated_at;

    /**
     * INVERSE SIDE
     * The customer_orders property is the inverse side of the association between customer and customer orders.
     * The association is joined over the customer id field and the userID field of the customer order.
     *
     * @var ArrayCollection<\WebWMS\Entity\Supplier>
     *
     * @ORM\OneToMany(targetEntity="WebWMS\Entity\SupplierOrder", mappedBy="supplier")
     */
    protected $supplier_orders;

    public function __construct()
    {
        $this->supplier_orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(mixed $id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getSupplierId(): mixed
    {
        return $this->supplier_id;
    }

    /**
     * @param mixed $supplier_id
     */
    public function setSupplierId(mixed $supplier_id): void
    {
        $this->supplier_id = $supplier_id;
    }

    /**
     * @return mixed
     */
    public function getSupplierNr(): mixed
    {
        return $this->supplier_nr;
    }

    /**
     * @param mixed $supplier_nr
     */
    public function setSupplierNr(mixed $supplier_nr): void
    {
        $this->supplier_nr = $supplier_nr;
    }

    /**
     * @return mixed
     */
    public function getSupplierName(): mixed
    {
        return $this->supplier_name;
    }

    /**
     * @param mixed $supplier_name
     */
    public function setSupplierName(mixed $supplier_name): void
    {
        $this->supplier_name = $supplier_name;
    }

    /**
     * @return mixed
     */
    public function getSupplierAddressAddition(): mixed
    {
        return $this->supplier_address_addition;
    }

    /**
     * @param mixed $supplier_address_addition
     */
    public function setSupplierAddressAddition(mixed $supplier_address_addition): void
    {
        $this->supplier_address_addition = $supplier_address_addition;
    }

    /**
     * @return mixed
     */
    public function getSupplierAddressStreet(): mixed
    {
        return $this->supplier_address_street;
    }

    /**
     * @param mixed $supplier_address_street
     */
    public function setSupplierAddressStreet(mixed $supplier_address_street): void
    {
        $this->supplier_address_street = $supplier_address_street;
    }

    /**
     * @return mixed
     */
    public function getSupplierAddressStreetNr(): mixed
    {
        return $this->supplier_address_street_nr;
    }

    /**
     * @param mixed $supplier_address_street_nr
     */
    public function setSupplierAddressStreetNr(mixed $supplier_address_street_nr): void
    {
        $this->supplier_address_street_nr = $supplier_address_street_nr;
    }

    /**
     * @return mixed
     */
    public function getSupplierAddressCountryCode(): mixed
    {
        return $this->supplier_address_country_code;
    }

    /**
     * @param mixed $supplier_address_country_code
     */
    public function setSupplierAddressCountryCode(mixed $supplier_address_country_code): void
    {
        $this->supplier_address_country_code = $supplier_address_country_code;
    }

    /**
     * @return mixed
     */
    public function getSupplierAddressZipcode(): mixed
    {
        return $this->supplier_address_zipcode;
    }

    /**
     * @param mixed $supplier_address_zipcode
     */
    public function setSupplierAddressZipcode(mixed $supplier_address_zipcode): void
    {
        $this->supplier_address_zipcode = $supplier_address_zipcode;
    }

    /**
     * @return mixed
     */
    public function getSupplierAddressCity(): mixed
    {
        return $this->supplier_address_city;
    }

    /**
     * @param mixed $supplier_address_city
     */
    public function setSupplierAddressCity(mixed $supplier_address_city): void
    {
        $this->supplier_address_city = $supplier_address_city;
    }

    public function toArray(): array
    {
        return [
            'supplier_id' => $this->supplier_id,
            'supplier_nr' => $this->supplier_nr,
            'supplier_name' => $this->supplier_name,
            'supplier_address_addition' => $this->supplier_address_addition,
            'supplier_address_street' => $this->supplier_address_street,
            'supplier_address_street_nr' => $this->supplier_address_street_nr,
            'supplier_address_country_code' => $this->supplier_address_country_code,
            'supplier_address_zipcode' => $this->supplier_address_zipcode,
            'supplier_address_city' => $this->supplier_address_city,
        ];
    }

    /**
     * Returns an array collection of WebWMS\Entity\SupplierOrder model instances, which
     * contains all data about the a single supplier order. The association is defined over
     * the Supplier.supplier_orders property (INVERSE SIDE) and the SupplierOrder.supplier (OWNING SIDE) property.
     * The order data is joined over the supplier_orders.supplier_id field.
     *
     * @return ArrayCollection<\WebWMS\Entity\SupplierOrder>
     */
    public function getSupplierOrders(): ArrayCollection
    {
        return $this->supplier_orders;
    }

    /**
     * Setter function for the orders association property which contains many instances of the WebWMS\Entity\SupplierOrder model which
     * contains all data about the a single supplier order. The association is defined over
     * the Supplier.orders property (INVERSE SIDE) and the SupplierOrder.supplier (OWNING SIDE) property.
     * The order data is joined over the supplier_orders.supplier_id field.
     *
     * @param ArrayCollection<\WebWMS\Entity\SupplierOrder>|null $supplier_orders
     *
     * @return Supplier
     */
    public function setSupplierOrders($supplier_orders)
    {
        $this->supplier_orders = $supplier_orders;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSupplierCreatedAt(): mixed
    {
        return $this->supplier_created_at;
    }

    /**
     * @param mixed $supplier_created_at
     */
    public function setSupplierCreatedAt(mixed $supplier_created_at): void
    {
        $this->supplier_created_at = $supplier_created_at;
    }

    /**
     * @return mixed
     */
    public function getSupplierUpdatedAt(): mixed
    {
        return $this->supplier_updated_at;
    }

    /**
     * @param mixed $supplier_updated_at
     */
    public function setSupplierUpdatedAt(mixed $supplier_updated_at): void
    {
        $this->supplier_updated_at = $supplier_updated_at;
    }
}
