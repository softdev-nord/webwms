<?php

declare(strict_types=1);

namespace WebWMS\Entity;

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
    private ?int $customerId;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $customerNr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $customerName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $customerAddressAddition;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $customerAddressStreet;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private ?string $customerAddressStreetNr;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private ?string $customerCountryCode;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private ?string $customerZipCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $customerCity;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $customerCreatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private mixed $customerUpdatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

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

    public function getCustomerNr(): ?int
    {
        return $this->customerNr;
    }

    public function setCustomerNr(int $customerNr): self
    {
        $this->customerNr = $customerNr;

        return $this;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): self
    {
        $this->customerName = $customerName;

        return $this;
    }

    public function getCustomerAddressAddition(): ?string
    {
        return $this->customerAddressAddition;
    }

    public function setCustomerAddressAddition(?string $customerAddressAddition): self
    {
        $this->customerAddressAddition = $customerAddressAddition;

        return $this;
    }

    public function getCustomerAddressStreet(): ?string
    {
        return $this->customerAddressStreet;
    }

    public function setCustomerAddressStreet(string $customerAddressStreet): self
    {
        $this->customerAddressStreet = $customerAddressStreet;

        return $this;
    }

    public function getCustomerAddressStreetNr(): ?string
    {
        return $this->customerAddressStreetNr;
    }

    public function setCustomerAddressStreetNr(string $customerAddressStreetNr): self
    {
        $this->customerAddressStreetNr = $customerAddressStreetNr;

        return $this;
    }

    public function getCustomerCountryCode(): ?string
    {
        return $this->customerCountryCode;
    }

    public function setCustomerCountryCode(string $customerCountryCode): self
    {
        $this->customerCountryCode = $customerCountryCode;

        return $this;
    }

    public function getCustomerZipCode(): ?string
    {
        return $this->customerZipCode;
    }

    public function setCustomerZipCode(string $customerZipCode): self
    {
        $this->customerZipCode = $customerZipCode;

        return $this;
    }

    public function getCustomerCity(): ?string
    {
        return $this->customerCity;
    }

    public function setCustomerCity(string $customerCity): self
    {
        $this->customerCity = $customerCity;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customerId,
            'customer_nr' => $this->customerNr,
            'customer_name' => $this->customerName,
            'customer_address_addition' => $this->customerAddressAddition,
            'customer_address_street' => $this->customerAddressStreet,
            'customer_address_street_nr' => $this->customerAddressStreetNr,
            'customer_country_code' => $this->customerCountryCode,
            'customer_zip_code' => $this->customerZipCode,
            'customer_city' => $this->customerCity,
        ];
    }

    public function getCustomerCreatedAt(): mixed
    {
        return $this->customerCreatedAt;
    }

    public function setCustomerCreatedAt(mixed $customerCreatedAt): void
    {
        $this->customerCreatedAt = $customerCreatedAt;
    }

    public function getCustomerUpdatedAt(): mixed
    {
        return $this->customerUpdatedAt;
    }

    public function setCustomerUpdatedAt(mixed $customerUpdatedAt): void
    {
        $this->customerUpdatedAt = $customerUpdatedAt;
    }
}
