<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @package:    WebWMS\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        Customer
 */
#[ORM\Table(name: 'customer')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\CustomerRepository')]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'skip_null_values' => false,
                'groups' => ['customer:read'],
            ]
        ),
        new GetCollection(
            normalizationContext: [
                'skip_null_values' => false,
                'groups' => ['customer:read'],
            ]
        ),
        new Post(
            denormalizationContext: [
                'groups' => ['customer:write'],
            ]
        ),
        new Put(
            denormalizationContext: [
                'groups' => ['customer:write'],
            ]
        ),
        new Patch(
            denormalizationContext: [
                'groups' => ['customer:write'],
            ]
        ),
        new Delete(
            denormalizationContext: [
                'groups' => ['customer:write'],
            ]
        ),
    ],
    formats: ['json'],
    normalizationContext: ['groups' => ['customer:read']],
    denormalizationContext: ['groups' => ['customer:write']]
)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'customer_id', type: 'integer', nullable: false)]
    #[Groups(['customer:read', 'customer:write'])]
    private int $customerId;

    #[ORM\Column(name: 'customer_nr', type: 'integer', nullable: false)]
    #[Groups(['customer:read', 'customer:write'])]
    private int $customerNr;

    #[ORM\Column(name: 'customer_name', type: 'string', length: 255, nullable: false)]
    #[Groups(['customer:read', 'customer:write'])]
    private string $customerName;

    #[ORM\Column(name: 'customer_address_addition', type: 'string', length: 255, nullable: true)]
    #[Groups(['customer:read', 'customer:write'])]
    private ?string $customerAddressAddition;

    #[ORM\Column(name: 'customer_address_street', type: 'string', length: 255, nullable: false)]
    #[Groups(['customer:read', 'customer:write'])]
    private string $customerAddressStreet;

    #[ORM\Column(name: 'customer_address_street_nr', type: 'string', length: 10, nullable: false)]
    #[Groups(['customer:read', 'customer:write'])]
    private string $customerAddressStreetNr;

    #[ORM\Column(name: 'customer_country_code', type: 'string', length: 10, nullable: false)]
    #[Groups(['customer:read', 'customer:write'])]
    private string $customerCountryCode;

    #[ORM\Column(name: 'customer_zip_code', type: 'string', length: 10, nullable: false)]
    #[Groups(['customer:read', 'customer:write'])]
    private string $customerZipCode;

    #[ORM\Column(name: 'customer_city', type: 'string', length: 255, nullable: false)]
    #[Groups(['customer:read', 'customer:write'])]
    private string $customerCity;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    #[Groups(['customer:read', 'customer:write'])]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    #[Groups(['customer:read', 'customer:write'])]
    private ?\DateTimeInterface $updatedAt;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: CustomerOrder::class)]
    #[Groups(['customer:read'])]
    private Collection $customerOrder;

    public function __construct()
    {
        $this->customerOrder = new ArrayCollection();
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
            'customerId' => $this->customerId,
            'customerNr' => $this->customerNr,
            'customerName' => $this->customerName,
            'customerAddressAddition' => $this->customerAddressAddition,
            'customerAddressStreet' => $this->customerAddressStreet,
            'customerAddressStreetNr' => $this->customerAddressStreetNr,
            'customerCountryCode' => $this->customerCountryCode,
            'customerZipCode' => $this->customerZipCode,
            'customerCity' => $this->customerCity,
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

    /**
     * @return Collection<CustomerOrder>
     */
    public function getCustomerOrders(): Collection
    {
        return $this->customerOrder;
    }

    /**
     * @param Collection<CustomerOrder> $customerOrder
     * @return Customer
     */
    public function setCustomerOrders(Collection $customerOrder): Customer
    {
        $this->customerOrder = $customerOrder;

        return $this;
    }
}
