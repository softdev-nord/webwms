<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private ?int $supplierId;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $supplierNr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $supplierName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $supplierAddressAddition;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $supplierAddressStreet;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private ?string $supplierAddressStreetNr;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private ?string $supplierAddressCountryCode;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private ?string $supplierAddressZipcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $supplierAddressCity;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $supplierCreatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $supplierUpdatedAt;

    /**
     * INVERSE SIDE
     * The customer_orders property is the inverse side of the association between customer and customer orders.
     * The association is joined over the customer id field and the userID field of the customer order.
     *
     * @var Collection<Supplier>
     *
     * @ORM\OneToMany(targetEntity="WebWMS\Entity\SupplierOrder", mappedBy="supplier")
     */
    protected Collection $supplierOrders;

    public function __construct()
    {
        $this->supplierOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getSupplierId(): ?int
    {
        return $this->supplierId;
    }

    public function setSupplierId(?int $supplierId): void
    {
        $this->supplierId = $supplierId;
    }

    public function getSupplierNr(): ?int
    {
        return $this->supplierNr;
    }

    public function setSupplierNr(?int $supplierNr): void
    {
        $this->supplierNr = $supplierNr;
    }

    public function getSupplierName(): ?string
    {
        return $this->supplierName;
    }

    public function setSupplierName(?string $supplierName): void
    {
        $this->supplierName = $supplierName;
    }

    public function getSupplierAddressAddition(): ?string
    {
        return $this->supplierAddressAddition;
    }

    public function setSupplierAddressAddition(?string $supplierAddressAddition): void
    {
        $this->supplierAddressAddition = $supplierAddressAddition;
    }

    public function getSupplierAddressStreet(): ?string
    {
        return $this->supplierAddressStreet;
    }

    public function setSupplierAddressStreet(?string $supplierAddressStreet): void
    {
        $this->supplierAddressStreet = $supplierAddressStreet;
    }

    public function getSupplierAddressStreetNr(): ?string
    {
        return $this->supplierAddressStreetNr;
    }

    public function setSupplierAddressStreetNr(?string $supplierAddressStreetNr): void
    {
        $this->supplierAddressStreetNr = $supplierAddressStreetNr;
    }

    public function getSupplierAddressCountryCode(): ?string
    {
        return $this->supplierAddressCountryCode;
    }

    public function setSupplierAddressCountryCode(?string $supplierAddressCountryCode): void
    {
        $this->supplierAddressCountryCode = $supplierAddressCountryCode;
    }

    public function getSupplierAddressZipcode(): ?string
    {
        return $this->supplierAddressZipcode;
    }

    public function setSupplierAddressZipcode(?string $supplierAddressZipcode): void
    {
        $this->supplierAddressZipcode = $supplierAddressZipcode;
    }

    public function getSupplierAddressCity(): ?string
    {
        return $this->supplierAddressCity;
    }

    public function setSupplierAddressCity(?string $supplierAddressCity): void
    {
        $this->supplierAddressCity = $supplierAddressCity;
    }

    public function toArray(): array
    {
        return [
            'supplierId' => $this->supplierId,
            'supplierNr' => $this->supplierNr,
            'supplierName' => $this->supplierName,
            'supplierAddressAddition' => $this->supplierAddressAddition,
            'supplierAddressStreet' => $this->supplierAddressStreet,
            'supplierAddressStreetNr' => $this->supplierAddressStreetNr,
            'supplierAddressCountryCode' => $this->supplierAddressCountryCode,
            'supplierAddressZipcode' => $this->supplierAddressZipcode,
            'supplierAddressCity' => $this->supplierAddressCity,
        ];
    }

    /**
     * Returns an array collection of WebWMS\Entity\SupplierOrder model instances, which
     * contains all data about the a single supplier order. The association is defined over
     * the Supplier.supplier_orders property (INVERSE SIDE) and the SupplierOrder.supplier (OWNING SIDE) property.
     * The order data is joined over the supplier_orders.supplierId field.
     *
     * @return ArrayCollection|Collection
     */
    public function getSupplierOrders(): ArrayCollection|Collection

    {
        return $this->supplierOrders;
    }

    /**
     * Setter function for the orders association property which contains many instances of the WebWMS\Entity\SupplierOrder model which
     * contains all data about the a single supplier order. The association is defined over
     * the Supplier.orders property (INVERSE SIDE) and the SupplierOrder.supplier (OWNING SIDE) property.
     * The order data is joined over the supplier_orders.supplierId field.
     *
     * @param ArrayCollection|Collection<SupplierOrder>|null $supplierOrders
     *
     * @return Supplier
     */
    public function setSupplierOrders(ArrayCollection|Collection|null $supplierOrders): Supplier
    {
        $this->supplierOrders = $supplierOrders;

        return $this;
    }

    public function getSupplierCreatedAt(): ?\DateTimeInterface
    {
        return $this->supplierCreatedAt;
    }

    public function setSupplierCreatedAt(?\DateTimeInterface $supplierCreatedAt): void
    {
        $this->supplierCreatedAt = $supplierCreatedAt;
    }

    public function getSupplierUpdatedAt(): ?\DateTimeInterface
    {
        return $this->supplierUpdatedAt;
    }

    public function setSupplierUpdatedAt(?\DateTimeInterface $supplierUpdatedAt): void
    {
        $this->supplierUpdatedAt = $supplierUpdatedAt;
    }
}
