<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'customer')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\CustomerRepository')]
#[ApiResource]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'customer_id', type: 'integer', nullable: false)]
    private int $customerId;

    #[ORM\Column(name: 'customer_nr', type: 'integer', nullable: false)]
    private int $customerNr;

    #[ORM\Column(name: 'customer_name', type: 'string', length: 255, nullable: false)]
    private string $customerName;

    #[ORM\Column(name: 'customer_address_addition', type: 'string', length: 255, nullable: true)]
    private ?string $customerAddressAddition;

    #[ORM\Column(name: 'customer_address_street', type: 'string', length: 255, nullable: false)]
    private string $customerAddressStreet;

    #[ORM\Column(name: 'customer_address_street_nr', type: 'string', length: 10, nullable: false)]
    private string $customerAddressStreetNr;

    #[ORM\Column(name: 'customer_country_code', type: 'string', length: 10, nullable: false)]
    private string $customerCountryCode;

    #[ORM\Column(name: 'customer_zip_code', type: 'string', length: 10, nullable: false)]
    private string $customerZipCode;

    #[ORM\Column(name: 'customer_city', type: 'string', length: 255, nullable: false)]
    private string $customerCity;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt;

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getCustomerNr(): int
    {
        return $this->customerNr;
    }

    public function setCustomerNr(int $customerNr): self
    {
        $this->customerNr = $customerNr;

        return $this;
    }

    public function getCustomerName(): string
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

    public function getCustomerAddressStreet(): string
    {
        return $this->customerAddressStreet;
    }

    public function setCustomerAddressStreet(string $customerAddressStreet): self
    {
        $this->customerAddressStreet = $customerAddressStreet;

        return $this;
    }

    public function getCustomerAddressStreetNr(): string
    {
        return $this->customerAddressStreetNr;
    }

    public function setCustomerAddressStreetNr(string $customerAddressStreetNr): self
    {
        $this->customerAddressStreetNr = $customerAddressStreetNr;

        return $this;
    }

    public function getCustomerCountryCode(): string
    {
        return $this->customerCountryCode;
    }

    public function setCustomerCountryCode(string $customerCountryCode): self
    {
        $this->customerCountryCode = $customerCountryCode;

        return $this;
    }

    public function getCustomerZipCode(): string
    {
        return $this->customerZipCode;
    }

    public function setCustomerZipCode(string $customerZipCode): self
    {
        $this->customerZipCode = $customerZipCode;

        return $this;
    }

    public function getCustomerCity(): string
    {
        return $this->customerCity;
    }

    public function setCustomerCity(string $customerCity): self
    {
        $this->customerCity = $customerCity;

        return $this;
    }

    /**
     * @return array<string, int|string|null>
     */
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
