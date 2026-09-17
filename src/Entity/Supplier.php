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
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Repository\SupplierRepository;

#[ClassInformation(
    package: 'WebWMS\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'Supplier'
)]
#[ORM\Table(name: 'supplier')]
#[ORM\Entity(repositoryClass: SupplierRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'skip_null_values' => false,
                'groups' => ['supplier:read'],
            ]
        ),
        new GetCollection(
            normalizationContext: [
                'skip_null_values' => false,
                'groups' => ['supplier:read'],
            ]
        ),
        new Post(
            denormalizationContext: [
                'groups' => ['supplier:write'],
            ]
        ),
        new Put(
            denormalizationContext: [
                'groups' => ['supplier:write'],
            ]
        ),
        new Patch(
            denormalizationContext: [
                'groups' => ['supplier:write'],
            ]
        ),
        new Delete(
            denormalizationContext: [
                'groups' => ['supplier:write'],
            ]
        ),
    ],
    formats: ['json'],
    normalizationContext: ['groups' => ['supplier:read']],
    denormalizationContext: ['groups' => ['supplier:write']]
)]
class Supplier
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'supplier_id', type: Types::INTEGER, nullable: false)]
    #[Groups(['supplier:read', 'supplier:write'])]
    private int $supplierId;

    #[ORM\Column(name: 'supplier_nr', type: Types::INTEGER, nullable: false)]
    #[Groups(['supplier:read', 'supplier:write'])]
    private int $supplierNr;

    #[ORM\Column(name: 'supplier_name', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['supplier:read', 'supplier:write'])]
    private string $supplierName;

    #[ORM\Column(name: 'supplier_address_addition', type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['supplier:read', 'supplier:write'])]
    private ?string $supplierAddressAddition = null;

    #[ORM\Column(name: 'supplier_address_street', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['supplier:read', 'supplier:write'])]
    private string $supplierAddressStreet;

    #[ORM\Column(name: 'supplier_address_street_nr', type: Types::STRING, length: 10, nullable: false)]
    #[Groups(['supplier:read', 'supplier:write'])]
    private string $supplierAddressStreetNr;

    #[ORM\Column(name: 'supplier_address_country_code', type: Types::STRING, length: 10, nullable: false)]
    #[Groups(['supplier:read', 'supplier:write'])]
    private string $supplierAddressCountryCode;

    #[ORM\Column(name: 'supplier_address_zipcode', type: Types::STRING, length: 10, nullable: false)]
    #[Groups(['supplier:read', 'supplier:write'])]
    private string $supplierAddressZipcode;

    #[ORM\Column(name: 'supplier_address_city', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['supplier:read', 'supplier:write'])]
    private string $supplierAddressCity;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['supplier:read', 'supplier:write'])]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['supplier:read', 'supplier:write'])]
    private ?DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, SupplierOrder>
     */
    #[ORM\OneToMany(mappedBy: 'supplier', targetEntity: SupplierOrder::class)]
    #[Groups(['supplier:read'])]
    private Collection $supplierOrder;

    public function __construct()
    {
        $this->supplierOrder = new ArrayCollection();
    }

    public function getSupplierId(): int
    {
        return $this->supplierId;
    }

    public function setSupplierId(int $supplierId): void
    {
        $this->supplierId = $supplierId;
    }

    public function getSupplierNr(): int
    {
        return $this->supplierNr;
    }

    public function setSupplierNr(int $supplierNr): void
    {
        $this->supplierNr = $supplierNr;
    }

    public function getSupplierName(): string
    {
        return $this->supplierName;
    }

    public function setSupplierName(string $supplierName): void
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

    public function getSupplierAddressStreet(): string
    {
        return $this->supplierAddressStreet;
    }

    public function setSupplierAddressStreet(string $supplierAddressStreet): void
    {
        $this->supplierAddressStreet = $supplierAddressStreet;
    }

    public function getSupplierAddressStreetNr(): string
    {
        return $this->supplierAddressStreetNr;
    }

    public function setSupplierAddressStreetNr(string $supplierAddressStreetNr): void
    {
        $this->supplierAddressStreetNr = $supplierAddressStreetNr;
    }

    public function getSupplierAddressCountryCode(): string
    {
        return $this->supplierAddressCountryCode;
    }

    public function setSupplierAddressCountryCode(string $supplierAddressCountryCode): void
    {
        $this->supplierAddressCountryCode = $supplierAddressCountryCode;
    }

    public function getSupplierAddressZipcode(): string
    {
        return $this->supplierAddressZipcode;
    }

    public function setSupplierAddressZipcode(string $supplierAddressZipcode): void
    {
        $this->supplierAddressZipcode = $supplierAddressZipcode;
    }

    public function getSupplierAddressCity(): string
    {
        return $this->supplierAddressCity;
    }

    public function setSupplierAddressCity(string $supplierAddressCity): void
    {
        $this->supplierAddressCity = $supplierAddressCity;
    }

    /**
     * @return array<string, int|string|null>
     */
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

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<SupplierOrder>
     */
    public function getSupplierOrders(): Collection
    {
        return $this->supplierOrder;
    }

    /**
     * @param Collection<SupplierOrder> $supplierOrder
     */
    public function setSupplierOrders(Collection $supplierOrder): self
    {
        $this->supplierOrder = $supplierOrder;

        return $this;
    }
}
